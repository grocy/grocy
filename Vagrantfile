Vagrant.configure("2") do |config|
  config.vm.box = "debian/buster64"

  config.vm.provision "shell", path: "buildfiles/provision-vagrant.sh"
  config.vm.network "forwarded_port", guest: 80, host: 8000
  config.vm.synced_folder ".", "/vagrant", disabled: true
  config.vm.synced_folder ".", "/grocy", automount: true, owner: "www-data", group: "www-data"
end
