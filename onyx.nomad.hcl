job "onyx" {
  datacenters = ["dc1"]

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
        "traefik.http.routers.onyx.rule=Host(`onyx.l80.ru`)",
        "traefik.http.routers.onyx.tls=true",
        "traefik.http.routers.onyx.tls.certresolver=leRes",
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

    task "server" {
      vault {
        policies = ["access-secrets"]
      }

      driver = "docker"

      config {
        image = "ghcr.io/ast21/onyx:main"
        auth {
          username = "ast21"
          password = "${GITHUB_TOKEN}"
        }

        ports = ["http"]

        volumes = [
          ".env:/var/www/html/.env"
        ]
      }

      template {
        data = <<EOF
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
