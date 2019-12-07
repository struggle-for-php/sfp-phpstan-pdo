.DEFAULT_GOAL := help

.PHONY: help
help:
	@echo "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'

.PHONY: cs-check
cs-check: ## Run PHP_Codeniffer
	./vendor/bin/phpcs

.PHONY: phpstan
phpstan: ## Run phpstan
	./vendor/bin/phpstan analyse --level=max src tests

.PHONY: infection
infection: ## Run infection
	phpdbg -qrr ./vendor/bin/infection --no-progress

.PHONY: all
all: cs-check phpstan ## Run all checks

