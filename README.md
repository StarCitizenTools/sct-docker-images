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
