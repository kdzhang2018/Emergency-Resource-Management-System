SET GLOBAL event_scheduler = ON;
SHOW PROCESSLIST;


DROP EVENT IF EXISTS repairStart;
CREATE EVENT repairStart
  ON SCHEDULE EVERY 1 DAY
DO
  UPDATE Resource
    INNER JOIN Repair ON Resource.resourceID = Repair.resourceID_Rep
  SET Resource.status =  'in repair', Resource.availableDate = DATE_ADD(Repair.endDate, INTERVAL 1 DAY)
  WHERE Repair.startDate_R = CURDATE();


DROP EVENT IF EXISTS repairEnd;
CREATE EVENT repairEnd
  ON SCHEDULE EVERY 1 DAY
DO
UPDATE Resource
SET status = 'available', availableDate = NULL
WHERE resourceID IN
      (SELECT resourceID_Rep
       FROM Repair
       WHERE endDate = DATE_ADD(curdate(), INTERVAL -1 DAY));


DROP EVENT IF EXISTS requestOutdated;
CREATE EVENT requestOutdated
  ON SCHEDULE EVERY 1 DAY
DO
  DELETE FROM Request
  WHERE returnBy < curdate() AND requestID NOT IN
                            (SELECT requestID_D
                             FROM Deploy);

SHOW EVENTS;
SHOW CREATE EVENT repairStart;
SHOW CREATE EVENT repairEnd;
SHOW CREATE EVENT requestOutdated;