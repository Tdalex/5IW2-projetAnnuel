echo "# Updating sources"
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 8C718D3B5072E1F5
sudo apt-get update

echo "# Installing OpenJDK"
sudo apt-get install -y openjdk-8-jre

echo "# Downloading Elasticsearch 6.2.2"
curl -L -O https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-6.2.2.deb

echo "# Installing Elasticsearch"
sudo dpkg -i elasticsearch-6.2.2.deb

echo "# Deleting Elasticsearch deb file"
sudo rm elasticsearch-6.2.2.deb

echo "# Starting Elasticsearch"
sudo /etc/init.d/elasticsearch start
