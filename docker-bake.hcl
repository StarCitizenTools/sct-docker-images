variable "REGISTRY" {
  default = "ghcr.io"
}

variable "TAG" {
  default = "latest"
}

variable "UPDATE_COMPOSER_DEPENDENCIES" {
  default = "false"
}

variable "UPDATE_SYSTEM_DEPENDENCIES" {
  default = "false"
}

variable "UPDATE_PHP_EXTENSIONS" {
  default = "false"
}

group "default" {
  targets = ["mediawiki", "jobrunner", "nginx"]
}

# The official php:8.4-fpm image, built from the same Dockerfile docker-library
# publishes it from, for when a PHP security release is out but Docker Hub
# hasn't rebuilt yet. Pinned to the docker-library commit that moved 8.4 to
# 8.4.27; that Dockerfile checks the tarball's SHA256 and its release
# manager's GPG signature. Delete this target once mediawiki/Dockerfile is back
# on the official image.
target "php" {
  context    = "https://github.com/docker-library/php.git#e3eaff845f7a9f7259c31da54dce0320b47a3bbd:8.4/trixie/fpm"
  cache-from = ["type=registry,ref=ghcr.io/starcitizentools/sct-docker-images-cache:php"]
  cache-to   = ["type=registry,ref=ghcr.io/starcitizentools/sct-docker-images-cache:php,mode=max"]
}

target "mediawiki" {
  context    = "mediawiki"
  dockerfile = "Dockerfile"
  contexts = {
    php-base = "target:php"
  }
  tags = [
    "${REGISTRY}/starcitizentools/mediawiki:smw-latest",
    "${REGISTRY}/starcitizentools/mediawiki:smw-${TAG}",
  ]
  args = {
    UPDATE_COMPOSER_DEPENDENCIES = UPDATE_COMPOSER_DEPENDENCIES
    UPDATE_SYSTEM_DEPENDENCIES   = UPDATE_SYSTEM_DEPENDENCIES
    UPDATE_PHP_EXTENSIONS        = UPDATE_PHP_EXTENSIONS
  }
  cache-from = ["type=registry,ref=ghcr.io/starcitizentools/sct-docker-images-cache:mediawiki"]
  cache-to   = ["type=registry,ref=ghcr.io/starcitizentools/sct-docker-images-cache:mediawiki,mode=max"]
}

target "jobrunner" {
  context    = "jobrunner"
  dockerfile = "Dockerfile"
  contexts = {
    mediawiki = "target:mediawiki"
  }
  tags = [
    "${REGISTRY}/starcitizentools/mediawiki:smw-jobrunner-latest",
    "${REGISTRY}/starcitizentools/mediawiki:smw-jobrunner-${TAG}",
  ]
  cache-from = ["type=registry,ref=ghcr.io/starcitizentools/sct-docker-images-cache:jobrunner"]
  cache-to   = ["type=registry,ref=ghcr.io/starcitizentools/sct-docker-images-cache:jobrunner,mode=max"]
}

target "nginx" {
  context    = "nginx"
  dockerfile = "Dockerfile"
  contexts = {
    mediawiki = "target:mediawiki"
  }
  tags = [
    "${REGISTRY}/starcitizentools/nginx:latest",
    "${REGISTRY}/starcitizentools/nginx:${TAG}",
  ]
  cache-from = ["type=registry,ref=ghcr.io/starcitizentools/sct-docker-images-cache:nginx"]
  cache-to   = ["type=registry,ref=ghcr.io/starcitizentools/sct-docker-images-cache:nginx,mode=max"]
}
