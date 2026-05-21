const express = require('express');
const pool = require('./db');

const app = express();

app.use(express.json());

// Ruta de prueba
app.get('/hola-mundo', (req, res) => {
  res.send('Hello World!');
});

// 1. OBTENER TODOS LOS PRODUCTOS
app.get('/productos', async (req, res) => {
  try {
    const resultado = await pool.query('SELECT * FROM productos');
    res.json(resultado.rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// 2. CREAR PRODUCTO
app.post('/productos', async (req, res) => {
  try {
    const { nombre, precio } = req.body;

    const nuevoProducto = await pool.query(
      'INSERT INTO productos (nombre, precio) VALUES ($1, $2) RETURNING *',
      [nombre, precio]
    );

    res.status(201).json(nuevoProducto.rows[0]);

  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// 3. ELIMINAR PRODUCTO
app.delete('/productos/:id', async (req, res) => {
  try {
    const { id } = req.params;

    await pool.query(
      'DELETE FROM productos WHERE id = $1',
      [id]
    );

    res.json({
      mensaje: 'Producto eliminado correctamente'
    });

  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// 4. ACTUALIZAR COMPLETO
app.put('/productos/:id', async (req, res) => {

  const { id } = req.params;
  const { nombre, precio } = req.body;

  try {

    const resultado = await pool.query(
      'UPDATE productos SET nombre = $1, precio = $2 WHERE id = $3 RETURNING *',
      [nombre, precio, id]
    );

    if (resultado.rows.length === 0) {
      return res.status(404).json({
        error: 'Producto no encontrado'
      });
    }

    res.json({
      mensaje: 'Actualizado con éxito',
      producto: resultado.rows[0]
    });

  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// 5. ACTUALIZACIÓN PARCIAL
app.patch('/productos/:id', async (req, res) => {

  const { id } = req.params;
  const campos = req.body;

  if (Object.keys(campos).length === 0) {
    return res.status(400).json({
      error: 'No se enviaron campos'
    });
  }

  try {

    const llaves = Object.keys(campos);
    const valores = Object.values(campos);

    const setQuery = llaves
      .map((llave, index) => `${llave} = $${index + 1}`)
      .join(', ');

    const query = `
      UPDATE productos
      SET ${setQuery}
      WHERE id = $${llaves.length + 1}
      RETURNING *
    `;

    const resultado = await pool.query(
      query,
      [...valores, id]
    );

    if (resultado.rows.length === 0) {
      return res.status(404).json({
        error: 'Producto no encontrado'
      });
    }

    res.json({
      mensaje: 'Campo actualizado',
      producto: resultado.rows[0]
    });

  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// PUERTO 8010
const PORT = 8010;

app.listen(PORT, () => {
  console.log(`Servidor ejecutándose en puerto ${PORT}`);
});
