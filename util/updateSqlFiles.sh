#!/bin/bash

for i in `mysql -N -u$MYSQLUSER -p$MYSQLPW -h$MYSQLHOST $MYSQLDB -e 'SHOW TABLES'`
do
    mysqldump --opt --skip-add-drop-table -d -u$MYSQLUSER -p$MYSQLPW -h$MYSQLHOST $MYSQLDB $i > sql/$i.sql
    sed -i -r "s/AUTO_INCREMENT=[0-9]+//g" sql/$i.sql
	echo $i;
done 
