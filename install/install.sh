#! /bin/bash

# ----------------------------------------------------
## Alertiv Install Script
## Written by Caleb Angelino
## Copyright Sage Technology Solutions / Ronco 2023
# ----------------------------------------------------

# ----------------------------------------------------
## Formatting Prerequisites
# ----------------------------------------------------
bold=$(tput bold)
normal=$(tput sgr0)
NO_FORMAT="\033[0m"
C_LIME="\033[38;5;10m"

# ----------------------------------------------------
## FUNCTIONS
# ----------------------------------------------------
yes_or_no() {
    while true; do
        read -p "$* [y/n]: " yn
        case $yn in
            [Yy]*) return 0  ;;  
            [Nn]*) echo "Aborted" ; return  1 ;;
        esac
    done
}

validate_ip() {
    local ip="$1"
    # Validate IP address
    local ip_regex="^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$"
    if ! [[ $ip =~ $ip_regex ]]; then
        return 1
    else
        return 0
    fi
}

# Function to validate CIDR notation
validate_cidr() {
    local cidr="$1"
    local ip
    local mask
    local IFS
    IFS='/' read -r ip mask <<< "$cidr"
    if [[ -z $ip || -z $mask ]]; then
        return 1
    fi
    
    if validate_ip "$ip" ; then
        return 1
    fi

    # Validate subnet mask prefix length
    local prefix_length="${mask##*/}"
    if [[ ! $prefix_length =~ ^[0-9]+$ || $prefix_length -lt 0 || $prefix_length -gt 32 ]]; then
        return 1
    fi
    return 0
}

# ----------------------------------------------------
## SETUP
# ----------------------------------------------------

## Check if run as root/sudo
if ! [[ "$EUID" = 0 ]]; then
    echo "Must run as root or use sudo. Please try again!"
    exit
fi

# ----------------------------------------------------
## INTRO
# ----------------------------------------------------
echo -e "${C_LIME}Welcome to the Alertiv install!"
echo "©2023 Sage Technology Solutions"
echo "Written by Brian Ritchey / Caleb Angelino"
echo -e "-----------------------------------------${NO_FORMAT}"
echo ""
echo "This script will install Docker, and all containers"
echo "needed to run Alertiv."
yes_or_no "${bold}Do you want to continue?" || exit

# ----------------------------------------------------
## IP INFO
# ----------------------------------------------------
clear
echo -e "${normal}${C_LIME}Please enter the IP address you'd like to use"
echo -e "in CIDR notation. e.g. 192.168.5/24${NO_FORMAT}"

# Validate IP address in CIDR notation
while true; do
    read -rp "${bold}IP Address:${normal} " cidr
    if validate_cidr "$cidr"; then
        break
    else
        echo "Invalid CIDR notation. Please try again."
    fi
done

## DO SOMETHING WITH IP HERE

echo ""
echo ""
echo -e "${normal}${C_LIME}Please enter the gateway/router IP address"
# Validate IP address
while true; do
    read -rp "${bold}IP Address:${normal} " gateway
    if validate_ip "$gateway"; then
        break
    else
        echo "Invalid IP address. Please try again."
    fi
done

# ----------------------------------------------------
## INSTALL DOCKER
# ----------------------------------------------------
apt-get update
apt-get install ca-certificates curl gnupg -y
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg
echo \
  "deb [arch="$(dpkg --print-architecture)" signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  "$(. /etc/os-release && echo "$VERSION_CODENAME")" stable" | \
  tee /etc/apt/sources.list.d/docker.list > /dev/null
apt-get update
apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin -y
# Add docker group
groupadd docker

# ----------------------------------------------------
## REBOOT
# ----------------------------------------------------
yes_or_no "${bold}Install and setup complete! Ready to reboot?" && exit 