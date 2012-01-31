Cherry Framework
=============

This is a very light PHP MVC framework that I built for personal use to speed up client projects and 
take advantage of the MVC pattern without going overboard. The framework uses a model system that lets
you execute SQL with an active record like syntax.

Current contributers
-------

*[Andy Hamilton](http://www.github.com/andyhmltn)

Documentation
-------
A full documentation is in the works at the moment. For now take a look at the 'blog' example project
that I will put up in the next few days.

Get started
------------

1. Fork it.
2. Edit the config file to suit your application (config/config.php)
3. Open the command line tool (`python cmd.py`)
4. Set the URL of your application like it asks
5. Generate an Auth key (`generate key yourauthkey`)
6. Place this into the config file under the DEVELOPMENT_KEY constant.
7. Restart the command line tool supplying your key as an argument (`python cmd.py yourauthkey`)
6. Ask for help! (`help`)