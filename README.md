# 🧩 Comparador CPT

**Autor:** Jonathan Intagono & ChatGPT  
**Versión:** 1.0.0  
**Compatibilidad:** WordPress 6.x+, Tema Woodmart  
**Licencia:** GPL-2.0+

---

## 📖 Descripción

**Comparador CPT** es un plugin para WordPress que reutiliza la interfaz del **widget de comparación del tema Woodmart** y permite generar **tablas comparativas estáticas** mediante **shortcodes** con productos fijos.

Ideal para crear páginas de comparación de productos con diseño profesional, sin depender del comparador dinámico del tema.

---

## ⚙️ Características principales

- ✅ Reutiliza la plantilla del comparador del tema **Woodmart**.  
- 🧱 Permite generar **shortcodes personalizados** con productos definidos manualmente.  
- 🪄 Muestra la **tabla comparativa estática** directamente en cualquier página o entrada.  
- ⚡ Ligero y sin dependencias externas.  
- 🎯 Ideal para landing pages, comparativas de productos o secciones informativas.

---

## 🚀 Instalación

1. Descarga el archivo `comparador-cpt.zip` desde el repositorio o tu instalación local.  
2. En tu panel de WordPress, ve a **Plugins → Añadir nuevo → Subir plugin**.  
3. Sube el archivo `.zip` y actívalo.  
4. Asegúrate de tener el tema **Woodmart** activo.

---

## 💡 Uso

1. Usa el shortcode en una página o entrada, especificando los productos por ID:

   ```php
   [comparador_cpt productos="123,456,789"]
   ```

   Esto mostrará una **tabla comparativa estática** con los productos seleccionados.

2. Puedes insertar varios shortcodes en distintas secciones para crear comparaciones independientes.

3. El plugin usa la **vista visual del comparador Woodmart**, por lo que mantiene el mismo estilo.

---

## 🧠 Ejemplo práctico

En una página llamada “Comparativa de Laptops”, podrías colocar:

```php
[comparador_cpt productos="101,102,103"]
```

Y el resultado será una tabla comparativa estática entre esos tres productos, usando el mismo diseño del comparador nativo de Woodmart.

---

## 🛠️ Requisitos

- WordPress 6.0 o superior  
- Tema **Woodmart** activo  
- PHP 7.4 o superior  

---

## 🤝 Créditos

Desarrollado por **Jonathan Intagono** con apoyo técnico de **ChatGPT (OpenAI)**.  
Diseño e inspiración: **Tema Woodmart**.

---

## 📄 Licencia

Este plugin se distribuye bajo la **Licencia Pública General GNU v2.0 (GPL-2.0)**.  
Puedes usarlo, modificarlo y redistribuirlo libremente, siempre que mantengas la atribución correspondiente.
