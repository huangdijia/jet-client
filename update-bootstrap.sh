#!/bin/sh

BOOTSTRAP=`dirname $(realpath $0)`/src/bootstrap.php

cat <<EOT > ${BOOTSTRAP}
<?php

\$baseDir = realpath(__DIR__);
\$classMap = array(
EOT

for FILE in `find ./src -type f -name "*.php"`; do
    REALPATH=`echo ${FILE} |sed -r "s/^\.\/src//"`
    FILENAME="$(basename -- $FILE)"
    CLASS="${FILENAME%.php}"
    CLASSMAP="'${CLASS}' => \$baseDir . '${REALPATH}',"
    echo "    ${CLASSMAP}" >> ${BOOTSTRAP}
done

cat <<EOT >> ${BOOTSTRAP}
);

spl_autoload_register(function (\$class) use (\$classMap) {
    if (isset(\$classMap[\$class]) && is_file(\$classMap[\$class])) {
        require_once \$classMap[\$class];
    }
});
EOT