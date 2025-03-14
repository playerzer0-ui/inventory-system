$resourceGroupName = "mail_rg"  # Replace with your resource group name
$templateFile = "template.json"       # Path to the template.json file
$parametersFile = "parameters.json"   # Path to the parameters.json file
$location = "westeurope"

$exists = az group exists --name $resourceGroupName
if ($exists -eq $true) {
    Write-Host "Resource group already exists"
} else {
    Write-Host "Creating resource group..."
    az group create -n $resourceGroupName -l $location
}

Write-Host "deploy the mails! " -ForegroundColor Yellow
az deployment group create --resource-group $resourceGroupName --template-file $templateFile --parameters $parametersFile

$domain = az communication email domain list --email-service-name "mail-system" --resource-group "mail_rg" | ConvertFrom-Json

$json = az communication list-key -n "com-system" --resource-group $resourceGroupName | ConvertFrom-Json
$conn = $json.primaryConnectionString -split ";"

$endpoint = $conn[0] -replace "endpoint=", ""
$accessKey = $conn[0] -replace "accesskey=", ""

$email = "DoNotReply@$($domain[0].fromSenderDomain)"

Write-Host "endpoint: $endpoint" -ForegroundColor Green 
Write-Host "accessKey: $accessKey" -ForegroundColor Green 
Write-Host "email: $email" -ForegroundColor Green 