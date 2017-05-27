#!/bin/bash -p

DBHOST=""
DBNAME=""
DBUSER=""
DBPASS=""

DATESTAMP=$(date -u +%Y-%m-%d)
TIMESTAMP=$(date -u +%Y_%m_%dT%H%M%z)
BASEDIR="$(pwd)/snapshots/database"
TARGETDIR="${BASEDIR}/{$DATESTAMP}/${TIMESTAMP}"
COUNT=0
DST=0

function usage()
{
    echo "Parameters Available"
    echo ""
    echo "./db-backup.sh"
    echo "-H --help"
    echo "-u --dbuser"
    echo "-h --dbhost"
    echo "-n --dbname"
    echo "-p --dbpass"
    echo "-b --base-path"
    echo "--skip-table-dump"
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
        -p | --dbpass)
            DBPASS=$VALUE
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
        --skip-table-dump)
            DST=1
            ;;
        *)
            echo "ERROR: unknown parameter \"$PARAM\""
            usage
            exit 1
            ;;
    esac
    shift
done

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

while [[ $DBPASS == "" ]]; do
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

    if [[ $DST != 1 ]]; then
        read -p "Would you like to backup the tables into its own file? (y/n)" dst

        if [[ $dst == "y" ]]; then
            for t in $(mysql --skip-column-names -BA -h $DBHOST -u $DBUSER -p$DBPASS -D $DBNAME -e "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '${DBNAME}' AND TABLE_TYPE = 'BASE TABLE'")
            do
                echo "Dumping table: ${DBNAME}.${t}"
                mysqldump -h $DBHOST -u $DBUSER -p$DBPASS --single-transaction $DBNAME $t | gzip -9 > "${TARGETDIR}/${DBNAME}.${t}.sql.gz"
                COUNT=$(( COUNT + 1 ))
            done

            echo "${COUNT} tables dumped from database '${DBNAME}' into dir=${TARGETDIR}"
        fi
    fi
fi
