#!/bin/bash
# Script to fix MinIO bucket policy to allow public read access
# Run this inside Railway MinIO service shell

echo "Setting up MinIO client alias..."
mc alias set myminio http://localhost:9000 ${MINIO_ROOT_USER} ${MINIO_ROOT_PASSWORD}

echo "Creating bucket if not exists..."
mc mb myminio/daily-report --ignore-existing

echo "Setting bucket policy to public (download)..."
mc anonymous set download myminio/daily-report

echo "Verifying bucket policy..."
mc anonymous get myminio/daily-report

echo "Listing objects in bucket..."
mc ls myminio/daily-report/

echo "Done! Bucket should now be publicly accessible for downloads."
