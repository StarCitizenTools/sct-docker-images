variable "REGISTRY" {
  default = "ghcr.io"
}

variable "TAG" {
  default = "latest"
}

# ---------------------------------------------------------------------------
# Pinned sources. This file is the single manifest of what each image is built
# from; the Dockerfiles take these as build args and refuse to build without
# them. Bump a pin here, in a commit.
# ---------------------------------------------------------------------------

variable "MEDIAWIKI_BRANCH" {
  default = "REL1_46"
}

# Empty = the head of MEDIAWIKI_BRANCH at build time. Set to an exact commit
# to freeze core.
variable "MEDIAWIKI_COMMIT_HASH" {
  default = ""
}

# Weird Gloop's fork of mediawiki/services/jobrunner. Check
# https://github.com/weirdgloop/mediawiki-services-jobrunner/commits/weirdgloop/master
# before bumping.
variable "JOBRUNNER_REPO" {
  default = "https://github.com/weirdgloop/mediawiki-services-jobrunner.git"
}

variable "JOBRUNNER_COMMIT_HASH" {
  default = "6c10e770bd47f452aec22edba6fc8e7bd9d7a8ea"
}

# ---------------------------------------------------------------------------
# Per-build knobs, set by CI from workflow_dispatch inputs.
# ---------------------------------------------------------------------------

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

target "mediawiki" {
  context    = "mediawiki"
  dockerfile = "Dockerfile"
  tags = [
    "${REGISTRY}/starcitizentools/mediawiki:smw-latest",
    "${REGISTRY}/starcitizentools/mediawiki:smw-${TAG}",
  ]
  args = {
    MEDIAWIKI_BRANCH             = MEDIAWIKI_BRANCH
    MEDIAWIKI_COMMIT_HASH        = MEDIAWIKI_COMMIT_HASH
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
  args = {
    JOBRUNNER_REPO        = JOBRUNNER_REPO
    JOBRUNNER_COMMIT_HASH = JOBRUNNER_COMMIT_HASH
  }
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
