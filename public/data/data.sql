
INSERT INTO points (location)
VALUES (ST_GeomFromText('POINT(48.858844 2.294351)'));

INSERT INTO sites (nom, description, location)
VALUES ('Mon Site', 'Description du site', ST_GeomFromText('POINT(48.858844 2.304351)'));

INSERT INTO sites (nom, description, location)
VALUES ('Mon Site', 'Description du site', ST_GeomFromText('POINT(47.844 2.34441)'));

insert into source (nom) values ('Forage'),('Pompage'),('Gravitaire');

CREATE TABLE points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location GEOMETRY NOT NULL
);

INSERT INTO points (location)
VALUES (ST_GeomFromText('POINT(47.844 2.34441)'));

 SELECT s.*
            FROM site s
            JOIN zone z ON ST_Intersects(s.coord, z.coord)
            WHERE z.id = :zoneId


select z.* from zone z join site s on st_Intersects(s.coord, z.coord) where s.id = 15

INSERT INTO type (libelle, code, capacite, disposition, action_id, utilite, cout, type) VALUES 
('Bonbonne', 'CODE1', 120, 1, NULL, 'Utilité du type 1', 1500000, 'Matétiel'),
('Bonbonne', 'CODE2', 120, 0, NULL, 'Utilité du type 2', 1500000, 'Matériel'),
('Forage', 'CODE3', 50, 1, NULL, 'Utilité du type 3', 2000000, 'Infrastructure'),
('Camion citerne', 'CODE4', 60, 0, NULL, 'Utilité du type 4', 200000, 'Entite'),
('Pluie artificielle', 'CODE5', 10000, 1, NULL, 'Utilité du type 5', 2400000, 'Entité');


insert into objectif(site_id,libelle,description,budget,deadline,estimation_cible,statut)
VALUES
    (1, 'Augmenter de la quantité d\'eau', 'Augmenter le stockage d\'eau dans la station de plus de 100 M3', 2000000, '2024-10-31', 100,'en cours');

insert into action (libelle,date_debut,avancement,objectif_id)
VALUES
    ('transport de 30m3 d\'eau par camion citerne par jours','2024-08-10',100,1);

INSERT INTO observation (libelle, date_heure,action_id)
VALUES 
    ('Inspection de la station', '2024-08-10 10:30:00',1),
    ('Arrivé du camion citerne', '2024-08-11 14:15:00',1);
INSERT INTO production(station_id, quantite, daty)
VALUES 
        (2,3145,'2024-07-15'),
        (2,2678,'2024-07-16'),
        (2,3521,'2024-07-17'),
        (2,2847,'2024-07-18'),
        (2,3259,'2024-07-19'),
        (2,2973,'2024-07-20'),
        (2,3560,'2024-07-21'),
        (2,2891,'2024-07-22'),
        (2,3342,'2024-07-23'),
        (2,3187,'2024-07-24'),
        (2,3725,'2024-07-25'),
        (2,2679,'2024-07-26'),
        (2,3156,'2024-07-27'),
        (2,2990,'2024-07-28'),
        (2,3480,'2024-07-29'),
        (2,2778,'2024-07-30'),
        (2,3245,'2024-07-31'),
        (2,2921,'2024-08-01'),
        (2,3098,'2024-08-02'),
        (2,3567,'2024-08-03'),
        (2,2824,'2024-08-04'),
        (2,3150,'2024-08-05'),
        (2,3699,'2024-08-06'),
        (2,2958,'2024-08-07'),
        (2,3412,'2024-08-08'),
        (2,3284,'2024-08-09'),
        (2,3117,'2024-08-10'),
        (2,3578,'2024-08-11'),
        (2,2835,'2024-08-12'),
        (2,3221,'2024-08-13'),
        (2,3095,'2024-08-14'),
        (2,3450,'2024-08-15'),
        (2,2904,'2024-08-16');

create or replace view ActionOfSite as 
select site.libelle,site.adresse from site join objectif on site.id=objectif.site_id

select objectif.libelle,count(action.libelle) as nbr from action join objectif on action.objectif_id=objectif.id
where action.date_debut > '2024-08-13'
group by objectif.id ;

INSERT INTO station (code,libelle)
VALUES
    ('SP100E','station1 ANALAMANGA'),
    ('SP100A','station2 ANALAMANGA'),
    ('SP100G','station3 ANALAMANGA');

INSERT INTO station (code,libelle,site_id)
VALUES ('SP104B','station1 Antsinanana',13);


INSERT INTO production (description, quantite, daty, gap, station_id) 
VALUES
    (NULL, 3145, '2024-01-01', NULL, 2),
    (NULL, 3200, '2024-01-02', NULL, 2),
    (NULL, 3100, '2024-01-03', NULL, 2),
    (NULL, 3185, '2024-01-04', NULL, 2),
    (NULL, 3050, '2024-01-05', NULL, 1),
    (NULL, 3125, '2024-01-06', NULL, 2),
    (NULL, 2980, '2024-01-07', NULL, 2),
    (NULL, 3230, '2024-01-08', NULL, 2),
    (NULL, 3090, '2024-01-09', NULL, 2),
    (NULL, 3155, '2024-01-10', NULL, 2),
    (NULL, 3105, '2024-01-11', NULL, 2),
    (NULL, 3170, '2024-01-12', NULL, 2),
    (NULL, 3055, '2024-01-13', NULL, 2),
    (NULL, 3120, '2024-01-14', NULL, 2),
    (NULL, 2995, '2024-01-15', NULL, 2),
    (NULL, 3205, '2024-01-16', NULL, 2),
    (NULL, 3140, '2024-01-17', NULL, 2),
    (NULL, 3010, '2024-01-18', NULL, 2),
    (NULL, 3080, '2024-01-19', NULL, 2),
    (NULL, 3160, '2024-01-20', NULL, 2),
    (NULL, 3100, '2024-01-21', NULL, 2),
    (NULL, 3190, '2024-01-22', NULL, 2),
    (NULL, 3065, '2024-01-23', NULL, 2),
    (NULL, 3110, '2024-01-24', NULL, 2),
    (NULL, 3155, '2024-01-25', NULL, 2),
    (NULL, 3085, '2024-01-26', NULL, 2),
    (NULL, 3120, '2024-01-27', NULL, 2),
    (NULL, 2985, '2024-01-28', NULL, 2),
    (NULL, 3200, '2024-01-29', NULL, 2),
    (NULL, 3115, '2024-01-30', NULL, 2),
    (NULL, 3070, '2024-01-31', NULL, 2);

INSERT INTO production (description, quantite, daty, gap, station_id) 
VALUES
    (NULL, 4500, '2024-01-01', NULL, 1),
    (NULL, 5200, '2024-01-02', NULL, 1),
    (NULL, 6300, '2024-01-03', NULL, 1),
    (NULL, 4800, '2024-01-04', NULL, 1),
    (NULL, 5400, '2024-01-05', NULL, 1),
    (NULL, 4700, '2024-01-06', NULL, 1),
    (NULL, 6000, '2024-01-07', NULL, 1),
    (NULL, 4900, '2024-01-08', NULL, 1),
    (NULL, 5600, '2024-01-09', NULL, 1),
    (NULL, 5100, '2024-01-10', NULL, 1),
    (NULL, 6700, '2024-01-11', NULL, 1),
    (NULL, 4500, '2024-01-12', NULL, 1),
    (NULL, 6200, '2024-01-13', NULL, 1),
    (NULL, 5900, '2024-01-14', NULL, 1),
    (NULL, 4000, '2024-01-15', NULL, 1),
    (NULL, 6500, '2024-01-16', NULL, 1),
    (NULL, 5300, '2024-01-17', NULL, 1),
    (NULL, 5800, '2024-01-18', NULL, 1),
    (NULL, 6200, '2024-01-19', NULL, 1),
    (NULL, 6700, '2024-01-20', NULL, 1),
    (NULL, 5100, '2024-01-21', NULL, 1),
    (NULL, 5600, '2024-01-22', NULL, 1),
    (NULL, 6000, '2024-01-23', NULL, 1),
    (NULL, 4900, '2024-01-24', NULL, 1),
    (NULL, 6900, '2024-01-25', NULL, 1),
    (NULL, 7000, '2024-01-26', NULL, 1),
    (NULL, 6600, '2024-01-27', NULL, 1),
    (NULL, 6500, '2024-01-28', NULL, 1),
    (NULL, 6200, '2024-01-29', NULL, 1),
    (NULL, 6400, '2024-01-30', NULL, 1),
    (NULL, 6100, '2024-01-31', NULL, 1);
INSERT INTO production (description, quantite, daty, gap, station_id) 
VALUES
    (NULL, 3500, '2024-01-01', NULL, 3),
    (NULL, 3600, '2024-01-02', NULL, 3),
    (NULL, 3100, '2024-01-03', NULL, 3),
    (NULL, 3200, '2024-01-04', NULL, 3),
    (NULL, 3150, '2024-01-05', NULL, 3),
    (NULL, 3300, '2024-01-06', NULL, 3),
    (NULL, 3400, '2024-01-07', NULL, 3),
    (NULL, 3000, '2024-01-08', NULL, 3),
    (NULL, 3800, '2024-01-09', NULL, 3),
    (NULL, 3700, '2024-01-10', NULL, 3),
    (NULL, 3650, '2024-01-11', NULL, 3),
    (NULL, 3900, '2024-01-12', NULL, 3),
    (NULL, 3350, '2024-01-13', NULL, 3),
    (NULL, 3550, '2024-01-14', NULL, 3),
    (NULL, 3450, '2024-01-15', NULL, 3),
    (NULL, 4000, '2024-01-16', NULL, 3),
    (NULL, 3750, '2024-01-17', NULL, 3),
    (NULL, 3250, '2024-01-18', NULL, 3),
    (NULL, 3100, '2024-01-19', NULL, 3),
    (NULL, 3200, '2024-01-20', NULL, 3),
    (NULL, 3800, '2024-01-21', NULL, 3),
    (NULL, 3900, '2024-01-22', NULL, 3),
    (NULL, 3600, '2024-01-23', NULL, 3),
    (NULL, 3300, '2024-01-24', NULL, 3),
    (NULL, 3100, '2024-01-25', NULL, 3),
    (NULL, 3400, '2024-01-26', NULL, 3),
    (NULL, 3000, '2024-01-27', NULL, 3),
    (NULL, 3500, '2024-01-28', NULL, 3),
    (NULL, 3650, '2024-01-29', NULL, 3),
    (NULL, 3700, '2024-01-30', NULL, 3),
    (NULL, 3550, '2024-01-31', NULL, 3);

INSERT INTO besoin (site_id,date_debut,quantite)
VALUES 
    (1, '2024-01-10',2000),
    (2, '2024-01-11',2000),
    (2, '2024-01-11',2100),
    (11, '2024-01-10',2000),
    (12, '2024-01-10',2000),
    (13, '2024-01-10',2000),
    (14, '2024-01-10',2000),
    (15, '2024-01-10',2000);

INSERT INTO besoin (site_id,date_debut,quantite)
VALUES 
    (16, '2024-01-10',1000),
    (17, '2024-01-11',1200),
    (18, '2024-01-11',1200),
    (19, '2024-01-10',1000),
    (20, '2024-01-10',900);

insert into source_station(station_id,source_id)
VALUES
    (1,2),
    (2,3),
    (3,1),
    (3,2),
    (4,1);
insert into source_station(station_id,source_id)
VALUES
    (9,3);

/*SELECT * 
FROM besoin b
WHERE b.site_id = 2
AND b.date_debut <= '2023-09-23'
AND (b.date_fin >= '2023-09-23' OR b.date_fin IS NULL)
LIMIT 1;


SELECT p.*
FROM Production p
INNER JOIN (
    SELECT station_id, daty, MAX(id) AS max_id
    FROM Production
    GROUP BY station_id, daty
) latest ON p.id = latest.max_id
ORDER BY p.daty DESC, p.station_id limit 10;


SELECT p.*
    FROM production p
    INNER JOIN (
        SELECT station_id, daty, MAX(id) AS max_id
        FROM production
        WHERE station_id = 2
        GROUP BY station_Id, daty
    ) latest ON p.id = latest.max_id
    ORDER BY p.daty DESC
    LIMIT 10*/

create or replace view productionUnique AS
SELECT p.*
FROM Production p
INNER JOIN (
    SELECT station_id, daty, MAX(id) AS max_id
    FROM Production
    GROUP BY station_id, daty
) latest ON p.id = latest.max_id
ORDER BY p.daty DESC, p.station_id


create or replace view siteProduction as
SELECT st.id as site_id,
date_format(p.daty,'%Y-%m-%d') AS date_production,
SUM(p.quantite) AS somme_production
FROM 
    productionUnique p
JOIN 
    station s ON p.station_id = s.id
JOIN 
    site st ON s.site_id = st.id
GROUP BY 
    p.daty, st.id
ORDER BY 
    p.daty DESC;

create or replace view productionMonth as
select site_id as id_site ,date_format(date_production,'%Y-%m') as mois,sum(somme_production) as quantite 
from siteProduction group by site_id ,mois order by mois;

create or replace view stationProductionMonth as
select station_id as id_station ,date_format(daty,'%Y-%m') as mois,sum(quantite) as quantite 
from productionUnique group by station_id ,mois order by mois;
