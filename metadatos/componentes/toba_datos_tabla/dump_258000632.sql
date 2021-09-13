------------------------------------------------------------
--[258000632]--  DT - persona_natural 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 258
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'libros', --proyecto
	'258000632', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'257000001', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'DT - persona_natural', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'libros', --fuente_datos_proyecto
	'libros', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2021-07-25 12:23:17', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 258

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'257000001', --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'persona_natural', --tabla
	NULL, --tabla_ext
	NULL, --alias
	'1', --modificar_claves
	'libros', --fuente_datos_proyecto
	'libros', --fuente_datos
	'1', --permite_actualizacion_automatica
	'cidig', --esquema
	'cidig'  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 258
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000727', --col_id
	'id_persona_natural', --columna
	'E', --tipo
	'1', --pk
	'persona_natural_id_persona_natural_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000728', --col_id
	'id_persona', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000729', --col_id
	'apyn', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'200', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000730', --col_id
	'fe_nac', --columna
	'F', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000731', --col_id
	'id_nacionalidad', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000732', --col_id
	'ciudad_nac', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'150', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000733', --col_id
	'provincia_nac', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'150', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000734', --col_id
	'id_nivel_educacion', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000735', --col_id
	'id_religion', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000736', --col_id
	'id_etnia', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000737', --col_id
	'id_sexo', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000738', --col_id
	'id_genero', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'libros', --objeto_proyecto
	'258000632', --objeto
	'258000739', --col_id
	'id_estado_civil', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'persona_natural'  --tabla
);
--- FIN Grupo de desarrollo 258
