# Alexander Tebiev - https://github.com/beeyev

.PHONY: *
.DEFAULT_GOAL := help

help: ## Show this help
	@printf "\n\033[37m%s\033[0m\n" 'Usage: make [target]'
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[32m%-14s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

stan: ## Execute PHPStan
	phpstan analyse --configuration=phpstan.neon.dist -v

cs: ## Execute PHP CS Fixer
	php-cs-fixer fix --diff -vv

test: ## Execute PHPUnit
	phpunit --testdox
