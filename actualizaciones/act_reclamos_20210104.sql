CREATE TRIGGER `reclamos_bi` BEFORE INSERT ON `reclamos` FOR EACH ROW  
begin
  if (new.numero is null) then 
    set new.numero = (select coalesce(max(numero), 0) from reclamos) + 1; 
  end if;
end