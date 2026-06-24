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

variable "MEDIAWIKI_COMMIT_HASH" {
  # Pinned MediaWiki REL1_43 commit. Bump deliberately by editing this line.
  # The workflow_dispatch input `mediawiki_commit_hash` overrides this for ad-hoc builds.
  default = "b9e0374db4c9f3f30223e4f02f0af17d8e0c979e"
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
