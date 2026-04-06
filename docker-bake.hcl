variable "REGISTRY" {
  default = "docker.io"
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

variable "MEDIAWIKI_COMMIT_HASH" {
  default = ""
}

group "default" {
  targets = ["mediawiki", "jobrunner", "nginx"]
}

target "mediawiki" {
  context    = "mediawiki"
  dockerfile = "Dockerfile"
  tags = [
    "${REGISTRY}/starcitizentools/mediawiki:smw-latest",
    "${REGISTRY}/starcitizentools/mediawiki:smw-${TAG}",
  ]
  args = {
    UPDATE_COMPOSER_DEPENDENCIES = UPDATE_COMPOSER_DEPENDENCIES
    UPDATE_SYSTEM_DEPENDENCIES   = UPDATE_SYSTEM_DEPENDENCIES
    UPDATE_PHP_EXTENSIONS        = UPDATE_PHP_EXTENSIONS
    MEDIAWIKI_COMMIT_HASH        = MEDIAWIKI_COMMIT_HASH
  }
  cache-from = ["type=gha,scope=mediawiki"]
  cache-to   = ["type=gha,mode=max,scope=mediawiki"]
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
  cache-from = ["type=gha,scope=jobrunner"]
  cache-to   = ["type=gha,mode=max,scope=jobrunner"]
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
  cache-from = ["type=gha,scope=nginx"]
  cache-to   = ["type=gha,mode=max,scope=nginx"]
}
