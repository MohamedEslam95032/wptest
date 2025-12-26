# release.sh Script

The `release.sh` script is a handy tool for preparing your plugin for release. It automates the process of cleaning up unnecessary files, running Composer to install only the required dependencies, and packaging the plugin into a zip file that is ready for distribution.

## Purpose

This script is designed to:

1. **Ensure a Clean Release**: The script removes development files, tests, and other non-essential items from the release package.
2. **Manage Dependencies**: It runs `composer install --no-dev` to ensure that only the necessary production dependencies are included in the final package.
3. **Automate Versioning**: The script extracts the version number from the main plugin file and includes it in the name of the release zip file.
4. **Package the Plugin**: Finally, it packages the cleaned-up plugin into a zip file, ready for deployment.

## How to Use the Script

1. **Ensure Composer is Installed**: Before running the script, make sure that [Composer](https://getcomposer.org/) is installed on your system.

2. **Run the Script**: Navigate to your plugin's root directory and run the script:

   ```bash
   ./release.sh
   ```

The script will:

- Create a temporary working directory.
- Copy all files and folders from your plugin into this temporary directory.
- Extract the version number from the main plugin file (e.g., `your-plugin.php`).
- Run `composer install --no-dev` to install only the production dependencies.
- Remove unnecessary files and folders listed in the script.
- Create a zip file in the `releases` directory, named with the plugin's folder name and version (e.g., `your-plugin.1.0.0.zip`).

3. **Check the Output**: The final zip file will be located in the `releases` directory in your plugin's root folder.

## Customizing the Script

If you need to customize the script for your specific needs, you can modify the following parts:

- **Unwanted Files**: The script has a list of unwanted files and folders that it deletes before packaging the plugin. You can add or remove items from this list according to your project's requirements.

  ```bash
  unwanted_files=(
      .dockerignore
      .env
      .gitignore
      .gitmodules
      .phpcs.xml.dist
      .travis.yml
      docker/
      # Add your own files or folders here
  )
  ```

- **Composer Install**: If your project requires additional Composer options, you can modify the `composer install` command.

- **Version Extraction**: The script extracts the version number from the main plugin file using a specific pattern. If your versioning format differs, you can adjust this line accordingly:

  ```bash
  version=$(grep -o "Version:[[:space:]]*[0-9.]*" "$temp_folder/$folder_name.php" | sed 's/Version:[[:space:]]*//')
  ```

## Conclusion

The `release.sh` script is a powerful tool for streamlining the release process of your WordPress plugin. By automating the cleanup and packaging steps, it ensures that your plugin is distributed in a clean, efficient, and versioned manner.

If you have any questions or run into issues, feel free to open an issue on our [GitHub repository](https://github.com/realtyna/wp-plugin-framework/issues).
