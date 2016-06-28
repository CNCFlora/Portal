all: build

build:
	docker build -t cncflora/portal .

push:
	docker push cncflora/portal
