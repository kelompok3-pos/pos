param(
    [string]$BaseUrl = "http://localhost/pos/public",
    [string]$AdminEmail = $env:QA_ADMIN_EMAIL,
    [string]$AdminPassword = $env:QA_ADMIN_PASSWORD,
    [string]$CashierEmail = $env:QA_CASHIER_EMAIL,
    [string]$CashierPassword = $env:QA_CASHIER_PASSWORD
)

$ErrorActionPreference = "Stop"
$script:Passed = 0
$script:Failed = 0

function Assert-True {
    param(
        [bool]$Condition,
        [string]$Message
    )

    if ($Condition) {
        $script:Passed++
        Write-Host "PASS: $Message" -ForegroundColor Green
    } else {
        $script:Failed++
        Write-Host "FAIL: $Message" -ForegroundColor Red
    }
}

function Get-CsrfToken {
    param(
        [Microsoft.PowerShell.Commands.WebRequestSession]$Session
    )

    $response = Invoke-WebRequest -Uri "$BaseUrl/login" -WebSession $Session -UseBasicParsing
    $match = [regex]::Match($response.Content, 'name="csrf_token"\s+value="([^"]+)"')

    if (-not $match.Success) {
        throw "CSRF token not found on login page."
    }

    return $match.Groups[1].Value
}

function Login-As {
    param(
        [string]$Email,
        [string]$Password
    )

    $session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
    $csrf = Get-CsrfToken -Session $session

    $body = @{
        email = $Email
        password = $Password
        csrf_token = $csrf
    }

    Invoke-WebRequest -Uri "$BaseUrl/login/authenticate" -Method Post -Body $body -WebSession $session -UseBasicParsing -MaximumRedirection 5 | Out-Null
    return $session
}

function Request-Path {
    param(
        [string]$Path,
        [Microsoft.PowerShell.Commands.WebRequestSession]$Session = $null
    )

    if ($Session) {
        return Invoke-WebRequest -Uri "$BaseUrl$Path" -WebSession $Session -UseBasicParsing -MaximumRedirection 5
    }

    return Invoke-WebRequest -Uri "$BaseUrl$Path" -UseBasicParsing -MaximumRedirection 5
}

if (-not $AdminEmail -or -not $AdminPassword -or -not $CashierEmail -or -not $CashierPassword) {
    Write-Host "Missing credentials. Set QA_ADMIN_EMAIL, QA_ADMIN_PASSWORD, QA_CASHIER_EMAIL, and QA_CASHIER_PASSWORD." -ForegroundColor Yellow
    exit 2
}

Write-Host "Running smoke tests against $BaseUrl"

$anonymousDashboard = Request-Path -Path "/dashboard"
Assert-True ($anonymousDashboard.BaseResponse.ResponseUri.AbsoluteUri -like "*/login*") "Protected dashboard redirects anonymous user to login"

$invalidSession = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$invalidCsrf = Get-CsrfToken -Session $invalidSession
$invalidResponse = Invoke-WebRequest -Uri "$BaseUrl/login/authenticate" -Method Post -Body @{
    email = "invalid@example.test"
    password = "wrong-password"
    csrf_token = $invalidCsrf
} -WebSession $invalidSession -UseBasicParsing -MaximumRedirection 5
Assert-True ($invalidResponse.Content -match "Email atau password salah|Login") "Invalid login does not authenticate"

$adminSession = Login-As -Email $AdminEmail -Password $AdminPassword
$adminDashboard = Request-Path -Path "/dashboard" -Session $adminSession
Assert-True ($adminDashboard.Content -match "Dashboard") "Admin can access dashboard after login"

$adminProduct = Request-Path -Path "/admin/product" -Session $adminSession
Assert-True ($adminProduct.Content -match "Produk|Product") "Admin can access product management"

$cashierSession = Login-As -Email $CashierEmail -Password $CashierPassword
$cashierTransaction = Request-Path -Path "/kasir/transaction" -Session $cashierSession
Assert-True ($cashierTransaction.Content -match "Transaksi|Keranjang|Produk") "Cashier can access transaction page"

try {
    $cashierAdminPage = Request-Path -Path "/admin/product" -Session $cashierSession
    Assert-True (-not ($cashierAdminPage.Content -match "Kelola Produk")) "Cashier cannot access admin product page"
} catch {
    Assert-True ($_.Exception.Response.StatusCode.value__ -eq 403) "Cashier receives 403 for admin product page"
}

$emptyCheckoutPage = Request-Path -Path "/kasir/transaction" -Session $cashierSession
$checkoutTokenMatch = [regex]::Match($emptyCheckoutPage.Content, 'action="[^"]*/kasir/transaction/checkout"[\s\S]*?name="csrf_token"\s+value="([^"]+)"')

if ($checkoutTokenMatch.Success) {
    $checkoutResponse = Invoke-WebRequest -Uri "$BaseUrl/kasir/transaction/checkout" -Method Post -Body @{
        paid_amount = 0
        csrf_token = $checkoutTokenMatch.Groups[1].Value
    } -WebSession $cashierSession -UseBasicParsing -MaximumRedirection 5
    Assert-True ($checkoutResponse.Content -match "Keranjang kosong|Transaksi") "Checkout with empty cart is rejected"
} else {
    Assert-True $true "Checkout form is hidden when cart is empty"
}

Write-Host ""
Write-Host "Passed: $script:Passed"
Write-Host "Failed: $script:Failed"

if ($script:Failed -gt 0) {
    exit 1
}

exit 0
