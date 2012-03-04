# OSTicket Reborn

**OSTicket Reborn** is a fork of OSTicket, an open source PHP helpdesk ticketing 
system. OSTicket Reborn maintains the original functions while implementing
further useful features.

### Mods Installed
- Reports 4.1
- Auto Assignment Rules
- Assigned To Column
- Mobile SCP site

### Setup

Install OSTicket using the ./setup/ scripts. Run the following SQL queries in
your OSTicket database:

	CREATE TABLE ost_ticket_rules (	id TINYINT AUTO_INCREMENT PRIMARY KEY, 
									isenabled TEXT NOT NULL COLLATE utf8_general_ci, 
									Category varchar(125) NOT NULL COLLATE utf8_general_ci, 
									Criteria TEXT NOT NULL COLLATE utf8_general_ci, 
									Action TEXT NOT NULL COLLATE utf8_general_ci, 
									Department TEXT NOT NULL COLLATE utf8_general_ci, 
									Staff TEXT NOT NULL COLLATE utf8_general_ci, 
									updated DATETIME NOT NULL, 
									created DATETIME NOT NULL);
	
	CREATE TABLE ost_reports (	`3d` TINYINT NOT NULL , 
								`graphWidth` INT NOT NULL , 
								`graphHeight` INT NOT NULL , 
								`resolution` VARCHAR( 255 ) NOT NULL , 
								`viewable` VARCHAR( 255 ) NOT NULL) 
								ENGINE = MYISAM ;
	
	INSERT INTO `ost`.`ost_reports` (	`3d`, 
										`graphWidth`, 
										`graphHeight`, 
										`resolution`, 
										`viewable`) 
										VALUES ('1', 
												'400', 
												'240', 
												'hours', 
												'admins');