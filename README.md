<div align="center">

# Star Citizen Wiki Docker

[Docker Hub](https://hub.docker.com/r/starcitizentools/mediawiki) | [Kubernetes config](https://github.com/StarCitizenTools/sct-k8-config)

</div>

The Docker configuration powering https://starcitizen.tools

## Images

| Image | Directory | Docker Hub Tag |
|-------|-----------|----------------|
| MediaWiki | `mediawiki/` | `starcitizentools/mediawiki:smw-latest` |
| Jobrunner | `jobrunner/` | `starcitizentools/mediawiki:smw-jobrunner-latest` |
| Nginx | `nginx/` | `starcitizentools/nginx:latest` |

## Pinned versions

`docker-bake.hcl` is the single manifest of what the images are built from:
the MediaWiki branch (and optional exact commit), the exact commit of the
jobrunner, and the exact tags of the base images (`php`, `nginx`, `composer`,
`php-extension-installer`). The Dockerfiles take these as build args or named
contexts and do not build without them, so a pin lives in one place. Bump a
pin by editing the `variable` block in a commit.

## Building

All images are built together using [Docker Bake](https://docs.docker.com/build/bake/):

```bash
docker buildx bake
```

To build a single image:

```bash
docker buildx bake mediawiki
docker buildx bake jobrunner
docker buildx bake nginx
```
