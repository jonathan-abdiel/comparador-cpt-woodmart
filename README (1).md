# 🧩 Comparador CPT

**Autor:** Jonathan Intagono & ChatGPT  
**Versión:** 1.0.0  
**Compatibilidad:** WordPress 6.x+, Tema Woodmart  
**Licencia:** GPL-2.0+

---

## 📖 Descripción

**Comparador CPT** crea un **CPT "Listas de comparación"** y reutiliza la **vista del comparador del tema Woodmart** para mostrar **tablas comparativas estáticas**.  
El uso típico es **insertar un shortcode** que referencia una lista previamente creada en el admin (**Menú: _Comparador_**).

---

## ⚙️ Características principales

- ✅ Crea el CPT **Listas de comparación** (menú **Comparador** en el admin).
- 🧱 Permite generar **tablas comparativas estáticas** mediante **shortcodes**.
- 🪄 Mantiene el **estilo del comparador de Woodmart** (solo toma la vista).
- ⚡ Ligero y sin dependencias externas.
- 🎯 Ideal para landings y páginas de comparativas controladas.

---

## 🚀 Instalación

1. Sube y activa el plugin `comparador-cpt` desde **Plugins → Añadir nuevo → Subir plugin**.  
2. Verifica que el tema **Woodmart** esté activo.  
3. En el admin verás el menú **Comparador** (CPT: _Listas de comparación_).

---

## 💡 Uso

### 1) Crear una lista
- Ve a **Comparador → Añadir nueva**.
- Agrega productos a la lista (UI de búsqueda y ordenación).  
- Publica la lista para obtener su **ID**.

### 2) Insertar en una página con shortcode
- Inserta el shortcode pasando el **ID de la lista**:

```php
[comparador_cpt id="123"]
```

> Esto renderiza la **tabla comparativa estática** usando la vista de Woodmart con los productos guardados en la lista `123`.

### 3) Varias comparativas en la misma página
Puedes colocar múltiples shortcodes con distintos IDs:

```php
[comparador_cpt id="101"]
[comparador_cpt id="202"]
```

---

## 🛠️ Requisitos

- WordPress 6.0+  
- Tema **Woodmart** activo  
- PHP 7.4+

---

## 🧩 Shortcode (API)

```
[comparador_cpt id="ID_DE_LISTA"]
```
- `id` (requerido): ID del CPT **compare_list** creado desde el menú **Comparador**.

---

## 🖼️ Captura sugerida

Añade una imagen en `assets/img/screenshot.jpg` y referencia en el README:

```markdown
## 🖼️ Captura de pantalla
![Comparativa estática](assets/img/screenshot.jpg)
```

---

## 🤝 Créditos

Desarrollado por **Jonathan Intagono** con apoyo técnico de **ChatGPT (OpenAI)**.  
Diseño e inspiración: **Tema Woodmart**.

---

## 📄 Licencia

GPL-2.0. Puedes usar, modificar y redistribuir manteniendo la atribución correspondiente.
