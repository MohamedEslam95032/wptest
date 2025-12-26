# Core Concepts

The Realtyna Base Plugin framework is built on a set of core concepts that provide a structured and modular approach to WordPress plugin development. This document outlines these core concepts, offering insights into how the framework is designed and how you can leverage its features to build robust and scalable plugins.

## Table of Contents
1. [Plugin Structure](#plugin-structure)
2. [Components](#components)
3. [Admin Pages](#admin-pages)
4. [Database Migrations](#database-migrations)
5. [Configuration](#configuration)
6. [Bootstrapping](#bootstrapping)
7. [Templates](#templates)
8. [Abstract Classes Overview](#abstract-classes-overview)

## 1. Plugin Structure

The framework organizes your plugin into distinct components, each with its own responsibilities. This section covers the directory structure and the purpose of key files and folders.

### Directory Structure

- **`src/`**: Core files and classes of your plugin.
- **`AdminPages/`**: Contains classes for WordPress admin pages.
- **`Boot/`**: Bootstrap files for initializing the plugin.
- **`Components/`**: Modular components that encapsulate specific functionality.
- **`Config/`**: Configuration files for your plugin.
- **`Database/`**: Handles database migrations.
- **`Main.php`**: Central file for initializing the plugin.
- **`Settings/`**: Manages plugin settings.

## 2. Components

Components are the building blocks of your plugin, allowing you to modularize functionality. This section explains how to create and manage components, including examples of typical components such as custom post types, admin pages, and more.

## 3. Admin Pages

Admin pages provide custom interfaces in the WordPress dashboard. This section covers how to create and register admin pages within your plugin.

## 4. Database Migrations

Database migrations manage changes to your plugin's database schema. This section explains how to create, register, and run migrations.

## 5. Configuration

Configuration is centralized in the `config.php` file, where you define your plugin’s settings. This section describes the key configuration options and how to manage them.

## 6. Bootstrapping

Bootstrapping is handled in the `Boot` directory, with key files like `App.php` and `Log.php` providing core functionality. This section details the bootstrapping process and its importance in initializing your plugin.

## 7. Templates

Templates are used to render custom admin pages and other UI elements. This section covers the template structure and how to integrate them into your plugin.

## 8. Abstract Classes Overview

Abstract classes provide a foundation for building components, admin pages, and other elements within your plugin. This section will delve into each abstract class provided by the framework, explaining its purpose and how to extend it for your own needs.

### Explanation of Each Abstract Class

- **[`ComponentAbstract`](abstracts/ComponentAbstract.md)**
- **[`AdminPageAbstract`](abstracts/AdminPageAbstract.md)**
- **[`AjaxHandlerAbstract`](abstracts/AjaxHandlerAbstract.md)**
- **[`CustomTaxonomyAbstract`](abstracts/CustomTaxonomyAbstract.md)**
- **[`PostTypeAbstract`](abstracts/PostTypeAbstract.md)**
- **[`RestApiEndpointAbstract`](abstracts/RestApiEndpointAbstract.md)**
- **[`ShortcodeAbstract`](abstracts/ShortcodeAbstract.md)**
- **[`WidgetAbstract`](abstracts/WidgetAbstract.md)**
- **[`MigrationAbstract`](abstracts/MigrationAbstract.md)**
