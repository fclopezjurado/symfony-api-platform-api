#!/usr/bin/env bash

aws --endpoint-url=http://localhost:4572 s3api create-bucket --bucket ${BUCKET} --region ${DEFAULT_REGION}