#!/usr/bin/env bash
start_time=$(date +%s)

script_version="1.0.0"

command="${2}"
plugin_name="${1}"
php_version="${3}"
flat_php_version=$(echo "${php_version}" | sed 's/\.//g')
container_name="${plugin_name}-tests-${flat_php_version}"
wordpress_container_name="${container_name}_wordpress_1"
db_container_name="${container_name}_db_1"
project_root_path=$(pwd)
codeception_envs_path="${project_root_path}/tests/codeception/_envs"
remote_path_in_container="/var/www/html"
cols=$(tput cols)

#######################################
# Echo the string as an step in the output.
# Arguments:
#   The message to display.
# Outputs:
#   The passed string after an arrow.
#######################################
echo_step() {
    echo "▶ ${1}"
}

#######################################
# Start the docker services calling docker-compose
# for the compose file related to the selected
# PHP version.
# Globals:
#   php_version
#   container_name
# Outputs:
#   Docker-compose output
#######################################
start_services() {
    docker-compose -f "./tests/docker/docker-compose-tests-${php_version}.yml" -p "${container_name}" up -d
}

#######################################
# Stop the docker services calling docker-compose
# for the compose file related to the selected
# PHP version.
# Globals:
#   php_version
#   container_name
# Outputs:
#   Docker-compose output
#######################################
stop_services() {
    docker-compose -f "./tests/docker/docker-compose-tests-${php_version}.yml" -p "${container_name}" down
}

#######################################
# Get the DB service IP address.
# Globals:
#   db_container_name
# Outputs:
#   The IP address
#######################################
get_db_service_ip() {
    docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' "${db_container_name}"
}

#######################################
# Get the WordPress service IP address.
# Globals:
#   wordpress_container_name
# Outputs:
#   The IP address
#######################################
get_wordpress_service_ip() {
    docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' "${wordpress_container_name}"
}

#######################################
# Show the IP addresses for all the services.
# Outputs:
#   The IP address
#######################################
get_ip_addresses() {
    wordpress_container_ip=$(get_wordpress_service_ip)
    db_container_ip=$(get_db_service_ip)

    echo "IP Addresses:"
    echo ""
    echo "wordrpess: ${wordpress_container_ip} port: 80"
    echo "       db: ${db_container_ip} port: 3306"
}

#######################################
# Get the paths for the mounted volume where
# WordPress is installed for the tests.
# Globals:
#   wordpress_container_name
# Outputs:
#   The path
#######################################
get_mount_path() {
    docker inspect -f '{{range.Mounts}}{{.Source}}{{end}}' "${wordpress_container_name}"
}

#######################################
# Fix permissions so current user can read
# and write files in the volume, if it in
# the group www-data.
# Globals:
#   wordpress_container_name
#   remote_path_in_container
#######################################
fix_volume_permissions() {
    docker exec "${wordpress_container_name}" find "${remote_path_in_container}" -type d -exec chmod 777 {} \;
    docker exec "${wordpress_container_name}" find "${remote_path_in_container}" -type f -exec chmod 666 {} \;
}

#######################################
# Create Codeception env files overriding
# the test params for matching the specific
# PHP version container addresses and data.
# Globals:
#   php_version
#   codeception_envs_path
#######################################
create_env_file() {
    env_file_name="${php_version}.yml"
    env_file_path="${codeception_envs_path}/${env_file_name}"
    template_file_path="${project_root_path}/tests/env.acceptance.template.yml"

    wordpress_container_ip=$(get_wordpress_service_ip)
    db_container_ip=$(get_db_service_ip)
    mount_path=$(get_mount_path)

    test_site_db_host="${db_container_ip}"
    test_site_db_port="3306"
    test_site_db_name="wordpress"
    test_site_db_user="wordpress"
    test_site_db_password="wordpress"
    test_site_wp_url="http:\/\/${wordpress_container_ip}"
    test_site_wp_domain="${wordpress_container_ip}"
    test_site_name="Tests on ${php_version}"
    test_site_admin_username="admin"
    test_site_admin_password="admin"

    # Remove current env file if exists
    # rm -rf "${env_file_path}" || true

    # Copy the template file and replace the variables
    cp "${template_file_path}" "${env_file_path}"
    sed -i "s/%TEST_SITE_DB_HOST%/${test_site_db_host}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_DB_PORT%/${test_site_db_port}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_DB_NAME%/${test_site_db_name}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_DB_USER%/${test_site_db_user}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_DB_PASSWORD%/${test_site_db_password}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_WP_URL%/${test_site_wp_url}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_WP_DOMAIN%/${test_site_wp_domain}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_NAME%/${test_site_name}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_ADMIN_USERNAME%/${test_site_admin_username}/g" "${env_file_path}"
    sed -i "s/%TEST_SITE_ADMIN_PASSWORD%/${test_site_admin_password}/g" "${env_file_path}"

    wp_root_folder=$(echo "${mount_path}" | sed "s/\//:::/g")
    sed -i "s/%WP_ROOT_FOLDER%/${wp_root_folder}/g" "${env_file_path}"
    sed -i "s/:::/\//g" "${env_file_path}"
}

#######################################
# Delete all the volumes containing the
# WordPress installation for tests.
# Globals:
#   project_root_path
#######################################
clean_volumes() {
    rm -rf "${project_root_path}/tests/docker/volumes/php*"
}

#######################################
# Delete all the env files.
# Globals:
#   codeception_envs_path
#######################################
clean_envs() {
    rm -f "${codeception_envs_path}/php*"
}

#######################################
# Add the current user to the www-data group,
# so it can read and write docker volume files.
#######################################
add_user_group() {
    sudo usermod -a -G www-data $(whoami)
}

#######################################
# Bootstrap the plugin into the container
# for being installed in the WordPress
# and tested.
#######################################
run_bootstrap() {
    mount_path=$(get_mount_path)
    tests/bin/bootstrap "${mount_path}"
}

#######################################
# Show a list of available envs for different
# PHP versions for testing.
# Outputs:
#   A list of codeception envs
#######################################
get_php_versions() {
    find tests/docker/ -type f -name 'docker-compose-tests-php*\.yml' | sed 's/tests\/docker\/docker-compose-tests-//g' | sed 's/\.yml//g'
}

#######################################
# Show the elapsed time since the script started.
# Globals:
#   start_time
# Outputs:
#   The runtime in seconds.
#######################################
show_time() {
    end_time=$(date +%s)
    runtime=$((end_time-start_time))
    echo ""
    echo "Runtime $runtime sec"
}

#######################################
# Repeats a string "n" times.
# Arguments:
#   The string to be repeated.
#   The number of times to repeat.
# Outputs:
#   The repeated string.
#######################################
repeat(){
	for (( c=1; c<="${2}"; c++ ))
    do
        echo -n "${1}"
    done
}

#######################################
# Show the header for the script, showing
# a few details of the plugin.
# Globals:
#   script_version
#   plugin_name
#   plugin_version
# Outputs:
#   The formatted header.
#######################################
echo_header() {
    repeat "=" $cols
    line=$(repeat "-" $(($cols-16)))
    echo "      __"
    echo "   -=(o '.      PUBLISHPRESS TESTS ASSISTENT - v${script_version}"
    echo "     '.-.\      ${line}"
    echo "     /|  \\      Name: ${plugin_name}"
    echo "     '|  ||     "
    echo "      _\_):,_   PHP Version: ${php_version}"
    echo ""
    repeat "=" $cols
    echo ""
}

#######################################
echo_header
case "${command}" in
    "setup")
        add_user_group
        ;;
    "start")
        echo "Start services:"
        echo ""
        echo_step "Starting the docker services"
        echo ""
        start_services
        echo ""

        echo_step "Fixing volume permissions"
        fix_volume_permissions

        echo_step "Bootstrapping"
        run_bootstrap

        echo_step  "Creating the codeception env file"
        echo ""
        create_env_file

        echo ""
        echo "Start process finished!"
        show_time
        ;;
    "bootstrap")
        echo "Bootstrapping the plugin for testing:"
        echo ""
        run_bootstrap

        echo ""
        echo "Plugin bootstrapped succesfully!"
        show_time
        ;;
    "start-all")

        ;;
    "stop")
        echo "Stop services:"
        echo ""

        echo_step "Stopping docker services"
        echo ""
        stop_services

        echo ""
        echo "Stop process finished!"
        show_time
        ;;
    "ip")
        get_ip_addresses
        ;;
    "php-versions")
        get_php_versions
        ;;
    "path")
        mount_path=$(get_mount_path)
        echo "WordPress path: ${mount_path}"
        ;;
    "clean")
        echo "Cleaning:"
        echo ""

        echo_step "Cleaning the volumes"
        clean_volumes

        echo_step "Cleaning the codeception env files"

        echo ""
        echo "Files cleaned succesfully!"
        clean_envs
        ;;
    *) echo "invalid option ${command}";;
esac