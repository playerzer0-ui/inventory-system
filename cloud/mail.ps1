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

az communication list-key -n "com-system" --resource-group $resourceGroupName

az communication email domain list --email-service-name "mail-system" --resource-group "mail_rg"