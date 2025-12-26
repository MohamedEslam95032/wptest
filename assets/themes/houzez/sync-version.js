#!/usr/bin/env node

/**
 * Sync version from style.css to package.json
 * This ensures version consistency across the theme
 */

const fs = require('fs');
const path = require('path');

// Read style.css
const stylePath = path.join(__dirname, 'style.css');
const packagePath = path.join(__dirname, 'package.json');

try {
    // Read style.css content
    const styleContent = fs.readFileSync(stylePath, 'utf8');
    
    // Extract version from style.css
    const versionMatch = styleContent.match(/Version:\s*([0-9.]+)/);
    
    if (!versionMatch || !versionMatch[1]) {
        console.error('Could not find version in style.css');
        process.exit(1);
    }
    
    const version = versionMatch[1];
    console.log(`Found version ${version} in style.css`);
    
    // Read package.json
    const packageJson = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
    
    // Check if version needs updating
    if (packageJson.version === version) {
        console.log('✓ package.json already has the correct version');
    } else {
        // Update version in package.json
        const oldVersion = packageJson.version;
        packageJson.version = version;
        
        // Write back to package.json
        fs.writeFileSync(packagePath, JSON.stringify(packageJson, null, 2) + '\n');
        console.log(`✓ Updated package.json version from ${oldVersion} to ${version}`);
    }
    
} catch (error) {
    console.error('Error syncing version:', error.message);
    process.exit(1);
}