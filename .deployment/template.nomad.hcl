# Using envsubst inside github actions
locals {
    domain    = "${DOMAIN}"
    image     = "${IMAGE}"
    image_tag = "${IMAGE_TAG}"
}

job "onyx" {
    datacenters = ["de1"]

    meta {
        image_tag = "${local.image_tag}"
    }

    group "svc" {
        count = 1

        network {
            mode = "bridge"

            port "http" {
                to = 8000
            }
        }

        service {
            tags = [
                "traefik.enable=true",
                "traefik.http.routers.${NOMAD_JOB_NAME}.rule=Host(`${local.domain}`)",
                "traefik.http.routers.${NOMAD_JOB_NAME}.tls=true",
                "traefik.http.routers.${NOMAD_JOB_NAME}.tls.certresolver=leRes",
            ]

            port = "http"

            check {
                type     = "tcp"
                interval = "10s"
                port     = "http"
                path     = "/up"
                timeout  = "5s"
            }
        }

        task "app" {
            vault {
                policies = ["access-secrets"]
            }

            driver = "docker"

            config {
                image = "${local.image}:${local.image_tag}"
                auth {
                    username = "${GITHUB_USER}"
                    password = "${GITHUB_TOKEN}"
                }

                ports = ["http"]
            }

            template {
                data        = <<EOF
GITHUB_USER="{{ with secret "secret/data/auth" }}{{ .Data.data.GITHUB_USER }}{{ end }}"
GITHUB_TOKEN="{{ with secret "secret/data/auth" }}{{ .Data.data.GITHUB_TOKEN }}{{ end }}"
EOF
                destination = "secrets/env"
                env         = true
            }

            template {
                data        = <<EOF
{{ with secret "secret/data/onyx" }}
{{ range $k, $v := .Data.data }}
{{ $k }}={{ $v }}
{{ end }}
{{ end }}
EOF
                destination = "local/env"
                env         = true
            }

            resources {
                cpu    = 200
                memory = 200
            }
        }
    }
}
