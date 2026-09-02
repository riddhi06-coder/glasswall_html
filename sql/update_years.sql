-- ============================================================
-- Add completion_year to projects + populate from client's year list.
-- Safe to run on local (glasswall) or the live server DB.
--   mysql -u root glasswall < sql/update_years.sql
-- or paste into phpMyAdmin > SQL on the server database.
-- ============================================================

ALTER TABLE projects
  ADD COLUMN IF NOT EXISTS completion_year VARCHAR(20) DEFAULT NULL AFTER floors;

-- ---------- RESIDENTIAL ----------
UPDATE projects SET completion_year='2022' WHERE slug='indiabulls-blu';
UPDATE projects SET completion_year='2025' WHERE slug='indiabulls-skyforest';   -- Indiabulls Forest
UPDATE projects SET completion_year='2024' WHERE slug='360-worli';              -- 360 Worli
UPDATE projects SET completion_year='2024' WHERE slug='artesia';               -- Artesia Metal Box
UPDATE projects SET completion_year='2023' WHERE slug='avana';                 -- Kalpataru Avana
UPDATE projects SET completion_year='2020' WHERE slug='lodha-world-one';        -- World One
UPDATE projects SET completion_year='2021' WHERE slug='the-42';
UPDATE projects SET completion_year='2023' WHERE slug='godrej-platinum';        -- Godrej Platinum B4
UPDATE projects SET completion_year='2014' WHERE slug='one-horizon-center';     -- (listed under residential)
UPDATE projects SET completion_year='2024' WHERE slug='one-avighna-park';
UPDATE projects SET completion_year='2017' WHERE slug='indiabulls-sky';
UPDATE projects SET completion_year='2020' WHERE slug='gulita';
UPDATE projects SET completion_year='2019' WHERE slug='the-park-3';             -- The Park
UPDATE projects SET completion_year='2022' WHERE slug='embassy-boulevard';
UPDATE projects SET completion_year='2019' WHERE slug='kingfisher-tower';       -- Kingfisher Tower
UPDATE projects SET completion_year='2021' WHERE slug='atmosphere';

-- ---------- COMMERCIAL ----------
UPDATE projects SET completion_year='2023' WHERE slug='rio-google';             -- Bagmane Rio-Google
UPDATE projects SET completion_year='2022' WHERE slug='wipro';                  -- Wipro IT SEZ
UPDATE projects SET completion_year='2021' WHERE slug='kohinoor-square-tower';  -- Kohinoor Square
UPDATE projects SET completion_year='2016' WHERE slug='mondeal-squares';        -- Mondeal Square
UPDATE projects SET completion_year='2025' WHERE slug='altimus';               -- Altimus Worli
UPDATE projects SET completion_year='2024' WHERE slug='delhi-international-airports-limited'; -- DIAL
UPDATE projects SET completion_year='2024' WHERE slug='sumadhura';             -- Sumadhura Capitol Towers
UPDATE projects SET completion_year='2023' WHERE slug='godrej-commercial-hebbal';
UPDATE projects SET completion_year='2020' WHERE slug='reliance-twin-tower';
UPDATE projects SET completion_year='2022' WHERE slug='etz';                    -- Embassy Tech Zone
UPDATE projects SET completion_year='2023' WHERE slug='taj-trees';             -- Taj The Trees
UPDATE projects SET completion_year='2023' WHERE slug='national-cancer-institute'; -- National Cancer Hospital
UPDATE projects SET completion_year='2022' WHERE slug='manyata';              -- Manyata Business Park Block 3
UPDATE projects SET completion_year='2023' WHERE slug='ankor-east';           -- Bagmane Angkor East
UPDATE projects SET completion_year='2013' WHERE slug='the-capital';
UPDATE projects SET completion_year='2024' WHERE slug='helium';               -- Bagmane Helium
UPDATE projects SET completion_year='2018' WHERE slug='flipkart';
UPDATE projects SET completion_year='2024' WHERE slug='taurus';               -- Bagmane Taurus
UPDATE projects SET completion_year='2025' WHERE slug='troy';
UPDATE projects SET completion_year='2019' WHERE slug='goldstone';            -- Bagmane Goldstone
UPDATE projects SET completion_year='2016' WHERE slug='j-w-marriott';         -- J.W. Marriott
UPDATE projects SET completion_year='2019' WHERE slug='dan-hotel';            -- The Dan
UPDATE projects SET completion_year='2026' WHERE slug='bagmane-radon';        -- Radon / Bagmane Radon
UPDATE projects SET completion_year='2025' WHERE slug='million-mind-tech-city';
UPDATE projects SET completion_year='2026' WHERE slug='prestige-tech-forest';
UPDATE projects SET completion_year='2026' WHERE slug='data-center';          -- Data Centre
UPDATE projects SET completion_year='Ongoing' WHERE slug='infosys-345';
UPDATE projects SET completion_year='Ongoing' WHERE slug='bagmane-lake-view';
UPDATE projects SET completion_year='Ongoing' WHERE slug='bagmane-memphis';   -- Memphis

-- ---------- INTERNATIONAL ----------
UPDATE projects SET completion_year='2025' WHERE slug='22-dry-dock';          -- 22-Drydock
UPDATE projects SET completion_year='2025' WHERE slug='albion-music-row';
UPDATE projects SET completion_year='2025' WHERE slug='dove';                 -- Roy Hill - Dove
UPDATE projects SET completion_year='2025' WHERE slug='ballys-chicago-casino-hotel';
UPDATE projects SET completion_year='2024' WHERE slug='26-32-jackson-ave';    -- Jackson Avenue
UPDATE projects SET completion_year='2024' WHERE slug='innovation-labs-at-harper-court'; -- Harper Court
UPDATE projects SET completion_year='2024' WHERE slug='spark-gtic';           -- Spark-GITC
UPDATE projects SET completion_year='2024' WHERE slug='2300-market-place';    -- 2300 - Market
UPDATE projects SET completion_year='2022' WHERE slug='3201-cuthbert';        -- 3201 - Cutberth
UPDATE projects SET completion_year='2022' WHERE slug='1400-south-wabash-ave';
UPDATE projects SET completion_year='2025' WHERE slug='adelaide-central-market-arcade'; -- Central Market Arcade Redevelopment
-- Mozoon Tower: no year provided in the list -> left blank
