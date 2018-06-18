echo "# Updating sources"
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 8C718D3B5072E1F5
sudo apt-get update

echo "# Installing OpenJDK"
sudo apt-get install -y openjdk-8-jre

echo "# Downloading Elasticsearch 5.6.9"
curl -L -O https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-5.6.9.deb

echo "# Installing Elasticsearch"
sudo dpkg -i elasticsearch-5.6.9.deb

echo "# Deleting Elasticsearch deb file"
sudo rm elasticsearch-5.6.9.deb

echo "# Starting Elasticsearch"
sudo /etc/init.d/elasticsearch start
