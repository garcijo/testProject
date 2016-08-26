# -*- mode: ruby -*-
# vi: set ft=ruby :
# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
$script = <<SCRIPT
  echo I am provisioning...
  date > vagrant_provisioned_at
  echo I am setting up nginx vhost configuration ...
  cp -rp /vagrant/.env_config/nginx/test1.loc.conf /etc/nginx/conf.d/
  service nginx restart
SCRIPT
Vagrant.configure(2) do |config|
  config.vm.box = "rasmus/php7dev"
  config.vm.provider "virtualbox" do |v|
    v.name = "my_vm"
  end
  #config.vm.customize ["modifyvm", :id, "--name", "Gangnam Style"]
  config.vm.network :private_network, ip: "10.0.60.12"
  config.vm.hostname = "test1.loc"
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.aliases = 'test1.loc'
  config.vm.synced_folder ".", "/vagrant", owner: "www-data", group: "www-data", mount_options: ['dmode=777']
  config.vm.provision "shell", inline: $script
end