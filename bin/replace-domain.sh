#!/bin/sh
sed -i 's_https://www.dismoi.io_http://localhost:8000_g' ./initdb.d/*.sql
sed -i 's_https://dismoi.io_http://localhost:8000_g' ./initdb.d/*.sql