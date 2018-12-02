# Set Up
- Import the database structure from `horseRace.sql`
- Add the connection details in `managers/ConfigManager.php` (especially `$db_user` und `$db_pass`)

# Structure
- `controllers`: Contains classes that handle user requests
- `interfaces`: Contains general interfaces
- `managers`: Contains classes that handle general stuff (eg the database or config)
- `misc`: Contains classes and files that don't fit anywhere (eg the autoloader)
- `models`: Contains the core models of the simulation
- `views`: Contains the view templates