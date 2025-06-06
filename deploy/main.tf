provider "google" {
  credentials = file("~/universidad/1er_cuatri_2025/PAW_2025/matchmaking-futbol-paw/deploy/secrets/credentials-matchmaking-app.json")
  project     = var.project_id
  region      = var.region
}

resource "google_compute_network" "vpc_network" {
  name = "matchmaking-vpc"
}

resource "google_container_cluster" "primary" {
  name     = "matchmaking-cluster"
  location = var.region

  remove_default_node_pool = true
  initial_node_count       = 1

  network = google_compute_network.vpc_network.name
}

resource "google_container_node_pool" "primary_nodes" {
  name       = "primary-node-pool"
  cluster    = google_container_cluster.primary.name
  location   = var.region

  node_config {
    machine_type = "e2-medium"
    disk_type    = "pd-standard"  # <- Esto evita usar SSD
    disk_size_gb = 30

    oauth_scopes = [
      "https://www.googleapis.com/auth/cloud-platform",
    ]
  }

  initial_node_count = 2
}
