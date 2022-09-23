pipeline {
    agent any

    stages {
        stage('Prepare') {
            steps {
                sh '/usr/bin/php7.4 /usr/bin/composer install --ignore-platform-reqs'
                sh 'rm -rf build/logs'
                sh 'mkdir -p build/logs'
            }
        }

        stage('PHP Syntax check') {
            steps {
                sh 'php -l app/code/Ecommerce121'
            }
        }

        stage('PHP Code Sniffer') {
            steps {
                sh '/usr/bin/php7.4 vendor/bin/phpcs --config-set installed_paths vendor/magento/magento-coding-standard'
                sh '/usr/bin/php7.4 vendor/bin/phpcs --report=checkstyle --report-file=build/logs/phpcs.xml --standard=Magento2 --extensions=php,phtml --ignore=autoload.php --ignore=vendor/ app/code/Ecommerce121 || exit 0'
                checkstyle pattern: 'build/logs/phpcs.xml'
            }
        }

        stage('PHPStan') {
            steps {
                sh '/usr/bin/php7.4 vendor/bin/phpstan analyse app/code/Ecommerce121 --no-interaction --no-progress --autoload-file vendor/ecommerce121/phpstan-magento2/bootstrap.php -c vendor/ecommerce121/phpstan-magento2/phpstan.neon --error-format=checkstyle --level=max | awk NF > build/logs/phpstan.xml || exit 0'
                checkstyle pattern: 'build/logs/phpstan.xml'
            }
        }

        stage('Copy paste detection') {
            steps {
                sh '/usr/bin/php7.4 vendor/bin/phpcpd --log-pmd build/logs/pmd-cpd.xml --exclude vendor app/code/Ecommerce121 || exit 0'
                dry canRunOnFailed: true, pattern: 'build/logs/pmd-cpd.xml'
            }
        }

        stage('Deploy on cloud') {
            steps {
                sshagent(['jenkins']) {
                    sh "git checkout ${params.BRANCH}"
                    sh "git pull origin ${params.BRANCH}"
                    sh "git pull magento ${params.BRANCH}"
                    sh "git push magento ${params.BRANCH}"
                }
            }
        }

        stage('Send Slack notification') {
            steps {
                wrap([$class: 'BuildUser']) {
                    sh "/var/lib/jenkins/deploy/cloud_slack.sh \"${params.SLACK_CHANNEL}\" ${params.ENVIRONMENT} ${params.ENVIRONMENT_URL} \"${BUILD_USER}\""
                }
            }
        }
    }
}
