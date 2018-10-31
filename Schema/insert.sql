-- CostPer
INSERT INTO CostPer VALUES ('hour');
INSERT INTO CostPer VALUES ('day');
INSERT INTO CostPer VALUES ('week');
INSERT INTO CostPer VALUES ('each');

-- ESF
INSERT INTO ESF VALUES (1, 'Transportation');
INSERT INTO ESF VALUES (2, 'Communications');
INSERT INTO ESF VALUES (3, 'Public Works and Engineering');
INSERT INTO ESF VALUES (4, 'Firefighting');
INSERT INTO ESF VALUES (5, 'Emergency Management');
INSERT INTO ESF VALUES (6, 'Mass Care, Emergency Assistance, Housing, and Human Services');
INSERT INTO ESF VALUES (7, 'Logistics Management and Resource Support');
INSERT INTO ESF VALUES (8, 'Public Health and Medical Services');
INSERT INTO ESF VALUES (9, 'Search and Rescue');
INSERT INTO ESF VALUES (10, 'Oil and Hazardous Materials Response');
INSERT INTO ESF VALUES (11, 'Agriculture and Natural Resources');
INSERT INTO ESF VALUES (12, 'Energy');
INSERT INTO ESF VALUES (13, 'Public Safety and Security');
INSERT INTO ESF VALUES (14, 'Long-Term Community Recovery');
INSERT INTO ESF VALUES (15, 'External Affairs');

-- User
INSERT INTO User VALUES('cityofatlanta', 'City of Atlanta', 'team063');
INSERT INTO Municipality VALUES ('cityofatlanta', 300154);
INSERT INTO User VALUES('cocacola', 'Coca Cola', 'team063');
INSERT INTO Company VALUES ('cocacola', 'Downtown Atlanta');
INSERT INTO User VALUES('fbi', 'FBI', 'team063');
INSERT INTO GovermentAgency VALUES ('fbi', 'Federal');
INSERT INTO User VALUES ('john', 'John', 'team063');
INSERT INTO Individual VALUES ('john', 'manager', '2011-11-11');

-- Resource
INSERT INTO Resource(name, model, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('All Terrain Vehicle', 'Honda', 'available', 33.7490, -84.380, 1, 'day', 100, 'cityofatlanta');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, availableDate, username_R)
VALUES ('Life Jackets', 'in repair', 32.7490, -87.380, 9, 'week', 200, '2016-12-10', 'fbi');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, availableDate, username_R)
VALUES ('Helicopter', 'in use', 34.7490, -85.380, 1, 'hour', 90, '2016-12-5', 'john');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, availableDate, username_R)
VALUES ('Ambulance', 'in use', 33.7490, -85.380, 8, 'each', 210, '2016-12-8', 'cityofatlanta');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('Gasoline Generator', 'available', 34.7490, -83.380, 6, 'day', 80, 'cocacola');
INSERT INTO Resource(name, model, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('PHP_Storm', 'Windows', 'available', 30.00, -85.000, 2, 'day', 40, 'cityofatlanta');
INSERT INTO Resource(name, model, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, availableDate, username_R)
VALUES ('Intellij', 'Windows', 'in use', 31.00, -86.000, 2, 'day', 50, '2016-12-21', 'cityofatlanta');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('Android Studio', 'available', 34.7490, -83.380, 5, 'day', 200, 'cocacola');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('AAAAA', 'available', 34.7490, -82.380, 10, 'day', 150, 'cocacola');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('BBBB', 'available', 34.7490, -84.380, 11, 'each', 300, 'cityofatlanta');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('CCCC', 'available', 32.7490, -86.380, 13, 'day', 100, 'fbi');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('DDDDD', 'available', 31.7490, -81.380, 15, 'hour', 100, 'john');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('EEEEE', 'available', 33.7490, -82.380, 10, 'week', 300, 'cityofatlanta');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, username_R)
VALUES ('FFFFFF', 'available', 36.7490, -84.380, 9, 'day', 100, 'fbi');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, availableDate, username_R)
VALUES ('GGGGG', 'in use', 34.7490, -86.380, 2, 'hour', 90, '2016-12-15', 'cocacola');
INSERT INTO Resource(name, status, latitude, longitude, ESFNumber_R, costUnit_R, costAmount, availableDate, username_R)
VALUES ('HHHHH', 'in repair', 31.7490, -83.380, 12, 'week', 300, '2016-12-18', 'cityofatlanta');


-- ResourceCapabilities
INSERT INTO ResourceCapabilities VALUES (3, 'GPS');
INSERT INTO ResourceCapabilities VALUES (4, 'GPS');
INSERT INTO ResourceCapabilities VALUES (6, 'waterproof');
INSERT INTO ResourceCapabilities VALUES (6, 'GPS');
INSERT INTO ResourceCapabilities VALUES (7, 'waterproof');

-- AdditionalESFs
INSERT INTO AdditionalESFs VALUES (5, 3);
INSERT INTO AdditionalESFs VALUES (1, 4);
INSERT INTO AdditionalESFs VALUES (3, 4);
INSERT INTO AdditionalESFs VALUES (2, 8);
INSERT INTO AdditionalESFs VALUES (3, 8);
INSERT INTO AdditionalESFs VALUES (1, 10);

-- Repair
-- repair in progress
INSERT INTO Repair VALUES (2, '2016-10-21', '2016-12-9');
INSERT INTO Repair VALUES (16, '2016-11-21', '2016-12-17');
-- repair scheduled
INSERT INTO Repair VALUES (4, '2016-12-5', '2016-12-7');
-- repair scheduled
INSERT INTO Repair VALUES (6, '2016-12-6', '2016-12-8');


-- Incident
INSERT INTO Incident(description, occurDate, latitude, longitude, username_Inc)
VALUES ('Flash Floods in Fulton County', '2016-10-22', 33.684, -86.224, 'cityofatlanta');
INSERT INTO Incident(description, occurDate, latitude, longitude, username_Inc)
VALUES ('Midtown Building Collapse', '2016-10-21', 32.684, -85.224, 'cocacola');
INSERT INTO Incident(description, occurDate, latitude, longitude, username_Inc)
VALUES ('North GA landslide', '2016-10-20', 35.684, -83.224, 'fbi');
INSERT INTO Incident(description, occurDate, latitude, longitude, username_Inc)
VALUES ('Fire in Fulton County', '2016-11-22', 34.684, -85.224, 'cityofatlanta');
INSERT INTO Incident(description, occurDate, latitude, longitude, username_Inc)
VALUES ('Uptown Building Collapse', '2016-11-21', 31.684, -87.224, 'fbi');
INSERT INTO Incident(description, occurDate, latitude, longitude, username_Inc)
VALUES ('West GA landslide', '2016-11-20', 34.684, -88.224, 'john');

-- Request
-- Life jacket: requested by incident 1 of cityofatlanta, not deployed
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (1, 2, '2016-12-10');
-- Helicopter: requested by incident 1 of cityofatlanta, deployed
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (1, 3, '2016-12-5');
INSERT INTO Deploy(requestID_D, startDate_D) VALUES (2, '2016-10-22');
-- Ambulance: requested by incident 3 of fbi, deployed
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (3, 4, '2016-12-4');
INSERT INTO Deploy(requestID_D, startDate_D) VALUES (3, '2016-10-21');
-- Gasoline generator: requested by incident 1 of cityofatlanta, not deployed
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (1, 5, '2016-12-2');
-- All terrain vehicle: requested by incident 2 of cococola to cityofatlanta, not deployed
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (2, 1, '2016-12-23');
-- PHP_Storm: deployed by incident 3 of fbi first and returned early, and then requested by incident 2, has scheduled repair
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (3, 6, '2016-12-05');
INSERT INTO Deploy(requestID_D, startDate_D, returnDate) VALUES (6, '2016-10-24', '2016-10-31');
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (2, 6, '2016-12-10');
-- Github: requested by incident 3 of fbi, deployed
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (3, 7, '2016-12-20');
INSERT INTO Deploy(requestID_D, startDate_D) VALUES (8, '2016-10-31');
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (4, 15, '2016-12-25');
INSERT INTO Deploy(requestID_D, startDate_D) VALUES (9, '2016-10-22');

INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (5, 13, '2016-12-10');
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (6, 13, '2016-12-11');
INSERT INTO Request(incidentID_Req, resourceID_Req, returnBy) VALUES (4, 14, '2016-12-12');