import React, { useState, useEffect } from "react";
import { APP_URL } from "../../App";
import PharmacistSidebar from "../../components/PharmacistSidebar";
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  Button,
  TextField,
  Typography,
  Grid,
} from "@mui/material";

const PharmacistInventory = () => {
  const [inventory, setInventory] = useState([]);
  const [formData, setFormData] = useState({
    MedicineName: "",
    Quantity: "",
    ExpiryDate: "",
    Price: "",
  });
  const [editingItem, setEditingItem] = useState(null);

  useEffect(() => {
    fetchInventory();
  }, []);

  const fetchInventory = async () => {
    const response = await fetch(`${APP_URL}/inventory.php`);
    const data = await response.json();
    setInventory(data);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const method = editingItem ? "PUT" : "POST";
    const response = await fetch(`${APP_URL}/inventory.php`, {
      method,
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(editingItem ? { ...formData, ID: editingItem } : formData),
    });
    const data = await response.json();
    if (data.status === 1) {
      fetchInventory();
      setFormData({ MedicineName: "", Quantity: "", ExpiryDate: "", Price: "" });
      setEditingItem(null);
    } else {
      alert(data.message);
    }
  };

  const handleEdit = (item) => {
    setFormData(item);
    setEditingItem(item.ID);
  };

  const handleDelete = async (id) => {
    const response = await fetch(`${APP_URL}/inventory.php`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ ID: id }),
    });
    const data = await response.json();
    if (data.status === 1) {
      fetchInventory();
    } else {
      alert(data.message);
    }
  };

  return (
    <div className="container-scroller">
      <div className="container-fluid page-body-wrapper">
        <PharmacistSidebar />
        <div className="main-panel">
          <div className="content-wrapper">
            <Typography variant="h4" gutterBottom style={{ color: "#007bff", fontWeight: "bold" }}>
              Pharmacist Inventory
            </Typography>
            <Paper style={{ padding: "20px", marginBottom: "20px" }}>
              <form onSubmit={handleSubmit}>
                <Grid container spacing={2}>
                  <Grid item xs={12} sm={6}>
                    <TextField
                      fullWidth
                      label="Medicine Name"
                      name="MedicineName"
                      value={formData.MedicineName}
                      onChange={handleInputChange}
                      required
                    />
                  </Grid>
                  <Grid item xs={12} sm={6}>
                    <TextField
                      fullWidth
                      label="Quantity"
                      type="number"
                      name="Quantity"
                      value={formData.Quantity}
                      onChange={handleInputChange}
                      required
                    />
                  </Grid>
                  <Grid item xs={12} sm={6}>
                    <TextField
                      fullWidth
                      label="Expiry Date"
                      type="date"
                      name="ExpiryDate"
                      value={formData.ExpiryDate}
                      onChange={handleInputChange}
                      InputLabelProps={{ shrink: true }}
                      required
                    />
                  </Grid>
                  <Grid item xs={12} sm={6}>
                    <TextField
                      fullWidth
                      label="Price"
                      type="number"
                      step="0.01"
                      name="Price"
                      value={formData.Price}
                      onChange={handleInputChange}
                      required
                    />
                  </Grid>
                  <Grid item xs={12}>
                    <Button
                      type="submit"
                      variant="contained"
                      color="primary"
                      fullWidth
                      style={{ backgroundColor: editingItem ? "#ffc107" : "#007bff" }}
                    >
                      {editingItem ? "Update" : "Add"} Item
                    </Button>
                  </Grid>
                </Grid>
              </form>
            </Paper>
            <TableContainer component={Paper}>
              <Table>
                <TableHead style={{ backgroundColor: "#007bff" }}>
                  <TableRow>
                    <TableCell style={{ color: "#fff", fontWeight: "bold" }}>Medicine Name</TableCell>
                    <TableCell style={{ color: "#fff", fontWeight: "bold" }}>Quantity</TableCell>
                    <TableCell style={{ color: "#fff", fontWeight: "bold" }}>Expiry Date</TableCell>
                    <TableCell style={{ color: "#fff", fontWeight: "bold" }}>Price</TableCell>
                    <TableCell style={{ color: "#fff", fontWeight: "bold" }}>Actions</TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {inventory.map((item) => (
                    <TableRow key={item.ID}>
                      <TableCell>{item.MedicineName}</TableCell>
                      <TableCell>{item.Quantity}</TableCell>
                      <TableCell>{item.ExpiryDate}</TableCell>
                      <TableCell>Rs {item.Price}</TableCell>
                      <TableCell>
                        <Button
                          variant="outlined"
                          color="primary"
                          size="small"
                          onClick={() => handleEdit(item)}
                          style={{ marginRight: "10px" }}
                        >
                          Edit
                        </Button>
                        <Button
                          variant="outlined"
                          color="secondary"
                          size="small"
                          onClick={() => handleDelete(item.ID)}
                        >
                          Delete
                        </Button>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </TableContainer>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PharmacistInventory;