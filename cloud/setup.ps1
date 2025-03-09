# Variables
$resourcegroup = "ca1_rg"
$vmname = "web-vm"
$location = "westeurope"
$sshKeyPath = "$env:USERPROFILE\.ssh\id_rsa.pub"  # Path to your public key

# Check if resource group exists
$exists = az group exists --name $resourcegroup
if ($exists -eq $true) {
    Write-Host "Resource group already exists"
} else {
    Write-Host "Creating resource group..."
    az group create -n $resourcegroup -l $location
}

# Create Public IP
Write-Host "Creating Public IP..." -ForegroundColor DarkYellow
$publicIp = az network public-ip create `
    --name web-vm-ip `
    --resource-group $resourcegroup `
    --allocation-method Static `
    --query publicIp.ipAddress `
    --output tsv

# Create VM with SSH Key
Write-Host "Creating VM..." -ForegroundColor DarkYellow
az vm create `
    -n $vmname `
    --resource-group $resourcegroup `
    --size Standard_B2s `
    --image 'Canonical:ubuntu-24_04-lts:server:latest' `
    --admin-username developer `
    --ssh-key-values $sshKeyPath `
    --public-ip-address web-vm-ip

# Open port 80
Write-Host "Opening port 80..." -ForegroundColor DarkYellow
az vm open-port -g $resourcegroup -n $vmname --port 80

# Wait for VM setup
Start-Sleep -Seconds 30  

# Get the assigned public IP
$publicIp = az vm show -d -g $resourcegroup -n $vmname --query publicIps -o tsv
Write-Host "VM Public IP: $publicIp" -ForegroundColor Green

# Copy files to VM
Write-Host "Copying files to VM..." -ForegroundColor DarkYellow
scp -i "$env:USERPROFILE\.ssh\id_rsa" .\vm_playbook.yml developer@${publicIp}:/home/developer/
scp -i "$env:USERPROFILE\.ssh\id_rsa" .\inventory.ini developer@${publicIp}:/home/developer/

# SSH into the VM
# Write-Host "Connecting to VM via SSH..." -ForegroundColor Cyan
# ssh -i "$env:USERPROFILE\.ssh\id_rsa" developer@$publicIp


#ansible-playbook -i inventory.ini vm_playbook.yml