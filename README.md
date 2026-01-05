
docker exec -it mysql-db mysql -uroot -p

SHOW DATABASES;
USE mydatabase;
SHOW TABLES;



sudo usermod -aG docker jenkins
sudo systemctl restart jenkins

----------------
                use these update version

sudo mkdir -p /usr/lib/docker/cli-plugins
sudo curl -SL https://github.com/docker/compose/releases/download/v2.25.0/docker-compose-linux-x86_64 \
  -o /usr/lib/docker/cli-plugins/docker-compose
sudo chmod +x /usr/lib/docker/cli-plugins/docker-compose
