# https://getcomposer.org/doc/articles/troubleshooting.md#proc-open-fork-failed-errors
sudo /bin/dd if=/dev/zero of=/var/swap.1 bs=1M count=1024
sudo /sbin/mkswap /var/swap.1
sudo /sbin/swapon /var/swap.1
