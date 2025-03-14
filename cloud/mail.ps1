$resourcegroup = "ca1_rg"
$location = "westeurope"

# Check if resource group exists
$exists = az group exists --name $resourcegroup
if ($exists -eq $true) {
    Write-Host "Resource group already exists"
} else {
    Write-Host "Creating resource group..."
    az group create -n $resourcegroup -l $location
}

# create communication service
Write-Host "create communication service" -ForegroundColor DarkYellow
az communication create --data-location "Europe" --name "com-system" --resource-group "ca1_rg" --location "Global"

# create email communication service
Write-Host "create email communication service" -ForegroundColor DarkYellow
az communication email create --data-location "Europe" --email-service-name "mail-system" --resource-group "ca1_rg"

# create subdomain
Write-Host "create subdomain" -ForegroundColor DarkYellow
az communication email domain create --domain-name AzureManagedDomain --email-service-name "mail-system" --location "Global" --resource-group "ca1_rg" --domain-management AzureManaged

# connect to com-service
Write-Host "connect to com-service" -ForegroundColor DarkYellow
$connectionString = az communication list-key --name "com-system" --resource-group "ca1_rg" --query "primaryConnectionString" -o tsv
az communication email update --resource-group "ca1_rg" --email-service-name "mail-system" --connection-string $connectionString
az communication email show --resource-group "ca1_rg" --email-service-name "mail-system"

# get mail
Write-Host "get mail, endpoint and key" -ForegroundColor DarkYellow
az communication email send list --resource-group "ca1_rg" --email-service-name "mail-system"
az communication list --resource-group "ca1_rg" --query "[?name=='com-system'].dataLocation" -o tsv
az communication list-keys --name "com-system" --resource-group "ca1_rg" --query "primaryKey" -o tsv


                    