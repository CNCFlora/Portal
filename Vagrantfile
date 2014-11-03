# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

$script = <<SCRIPT
apt-get update
apt-get install -y python-dev
apt-get install -y python-virtualenv

apt-get install -y libfontconfig
SCRIPT

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty32"

  config.vm.provision "shell", inline: $script

  config.vm.network "private_network", ip: "192.168.50.20"
  config.vm.network "forwarded_port", guest: 8889, host: 8889
end
