#!/bin/bash -p

DBHOST=""
DBNAME=""
DBUSER=""
DBPASS=""

TIMESTAMP=$(date +%Y-%m-%d)
BASEDIR="$(pwd)/snapshots/database"
TARGETDIR="${BASEDIR}/${TIMESTAMP}"
COUNT=0

function usage()
{
    echo "Parameters Available"
    echo ""
    echo "./db-backup.sh"
    echo "-H --help"
    echo "-u --dbuser"
    echo "-h --dbhost"
    echo "-n --dbname"
    echo "-b --base-path"
    echo ""
}

while [ "$1" != "" ]; do
    PARAM=`echo $1 | awk -F= '{print $1}'`
    VALUE=`echo $1 | awk -F= '{print $2}'`
    case $PARAM in
        -H | --help)
            usage
            exit
            ;;
        -u | --dbuser)
            DBUSER=$VALUE
            ;;
        -h | --dbhost)
            DBHOST=$VALUE
            ;;
        -n | --dbname)
            DBNAME=$VALUE
            ;;
        -b | --base-path)
            BASEDIR=$VALUE
            ;;
        *)
            echo "ERROR: unknown parameter \"$PARAM\""
            usage
            exit 1
            ;;
    esac
    shift
done

read -p "Enter directory path to store backups. Press enter to use default [${BASEDIR}]: " dbb

if [[ $dbb != "" ]]; then
    BASEDIR=$dbb
fi

while [[ $DBHOST == "" ]]; do
    read -p "Please enter the host address for the database: " dbh
    DBHOST=$dbh
done

while [[ $DBNAME == "" ]]; do
    read -p "Please enter the name for the database: " dbn
    DBNAME=$dbn
done

while [[ $DBUSER == "" ]]; do
    read -p "Please enter the username for the database: " dbu
    DBUSER=$dbu
done

while true; do
    read -s -p "Enter Password: " pass

    if [[ $pass == "" ]]; then
        echo "Password can not be blank!"
    elif [[ $pass != "" ]]; then
        DBPASS=$pass;
        break;
    fi
done

if [[ ! -d "$TARGETDIR" ]]; then
    mkdir -p $TARGETDIR
fi

if [[ -d "$TARGETDIR" ]]; then

    echo "Dumping database to single file: ${DBNAME}"
    mysqldump -h $DBHOST -u $DBUSER -p$DBPASS --single-transaction $DBNAME | gzip -9 > "${TARGETDIR}/${DBNAME}.sql.gz"

    for t in $(mysql --skip-column-names -BA -h $DBHOST -u $DBUSER -p$DBPASS -D $DBNAME -e "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '${DBNAME}' AND TABLE_TYPE = 'BASE TABLE'")
    do
        echo "Dumping table: ${DBNAME}.${t}"
        mysqldump -h $DBHOST -u $DBUSER -p$DBPASS --single-transaction $DBNAME $t | gzip -9 > "${TARGETDIR}/${DBNAME}.${t}.sql.gz"
        COUNT=$(( COUNT + 1 ))
    done

    echo "${COUNT} tables dumped from database '${DBNAME}' into dir=${TARGETDIR}"
fi
