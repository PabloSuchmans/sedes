CREATE TRIGGER `consorcios_bu` BEFORE UPDATE ON `consorcios` FOR EACH ROW  
begin
  set new.fecha_actualizacion=CURRENT_TIMESTAMP;
end