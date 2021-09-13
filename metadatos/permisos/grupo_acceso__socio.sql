
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'libros', --proyecto
	'socio', --usuario_grupo_acc
	'socio', --nombre
	NULL, --nivel_acceso
	NULL, --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'1', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'libros', --proyecto
	'socio', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 258
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'libros', --proyecto
	'socio', --usuario_grupo_acc
	NULL, --item_id
	'258000089'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'libros', --proyecto
	'socio', --usuario_grupo_acc
	NULL, --item_id
	'258000090'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'libros', --proyecto
	'socio', --usuario_grupo_acc
	NULL, --item_id
	'258000092'  --item
);
--- FIN Grupo de desarrollo 258

------------------------------------------------------------
-- apex_grupo_acc_restriccion_funcional
------------------------------------------------------------
INSERT INTO apex_grupo_acc_restriccion_funcional (proyecto, usuario_grupo_acc, restriccion_funcional) VALUES (
	'libros', --proyecto
	'socio', --usuario_grupo_acc
	'258000001'  --restriccion_funcional
);
INSERT INTO apex_grupo_acc_restriccion_funcional (proyecto, usuario_grupo_acc, restriccion_funcional) VALUES (
	'libros', --proyecto
	'socio', --usuario_grupo_acc
	'258000002'  --restriccion_funcional
);
