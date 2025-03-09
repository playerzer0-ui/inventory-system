#variables
$resourcegroup = "ca1_rg"
$vmname = "web-vm"

#make resource group
$exists = az group exists --name $resourcegroup
if ($exists -eq $true) {
    Write-Host "already exists"
}
else{
    Write-Host "not exists"
    az group create -n $resourcegroup -l westeurope
}

#make IP
Write-Host "make IP" -ForegroundColor DarkYellow
$publicIpResource = az network public-ip create `
    --name web-vm-ip `
    --resource-group ca1_rg `
    --allocation-method Static `
    --query publicIp.ipAddress

#make vm
Write-Host "make VM" -ForegroundColor DarkYellow

az vm create `
    -n web-vm `
    --resource-group ca1_rg `
    --size Standard_B2s `
    --image 'Canonical:ubuntu-24_04-lts:server:latest' `
    --admin-user developer `
    --admin-password password123$ `
    --custom-data vm_init.yml `
    --public-ip-address web-vm-ip

#open port
Write-Host "make port open" -ForegroundColor DarkYellow
az vm open-port -g $resourcegroup -n $vmname --port 80

#put the files in the vm
Write-Host "put the files in the vm" -ForegroundColor DarkYellow
scp .\vm_playbook.yml developer@${publicIpResource}:/home/developer/
scp .\inventory.ini developer@${publicIpResource}:/home/developer/

Write-Host "IP-address: " $publicIpResource -ForegroundColor DarkYellow

#ansible-playbook -i inventory.ini vm_playbook.yml