<template>
    <v-container class="d-flex justify-center align-center min-vh-100">
      <v-card class="pa-5" width="400">
        <v-card-title class="text-center text-h5">Login</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="login">
            <!-- Email Field -->
            <v-text-field 
              v-model="email" 
              label="Email" 
              type="email" 
              required
              outlined
              density="comfortable"
              class="mb-3"
              :error-messages="errors.email"
            ></v-text-field>
  
            <!-- Password Field -->
            <v-text-field 
              v-model="password" 
              label="Password" 
              type="password" 
              required
              outlined
              density="comfortable"
              class="mb-3"
              :error-messages="errors.password"
            ></v-text-field>
  
            <!-- Login Button -->
            <v-btn type="submit" color="blue" block>Login</v-btn>
  
            <!-- Global Error Message -->
            <p v-if="error" class="text-red mt-3 text-center">{{ error }}</p>
  
            <!-- Register Link -->
            <p class="text-center mt-4">
              New here? 
              <router-link to="/register" class="text-blue">Register Here</router-link>
            </p>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </template>
  
  <script>
  import axios from "axios";
  import { defineComponent } from "vue";
  import { VContainer, VCard, VCardTitle, VCardText, VForm, VTextField, VBtn } from "vuetify/components";
  
  export default defineComponent({
    components: {
      VContainer,
      VCard,
      VCardTitle,
      VCardText,
      VForm,
      VTextField,
      VBtn,
    },
    data() {
      return {
        email: "",
        password: "",
        error: "",
        errors: {}, // Stores validation errors
      };
    },
    methods: {
      async login() {
        this.error = "";
        this.errors = {}; // Clear previous errors
  
        try {
          const response = await axios.post("/login", {
            email: this.email,
            password: this.password,
          });
  
          localStorage.setItem("token", response.data.token);
          this.$router.push("/tasks");
        } catch (err) {
          if (err.response && err.response.status === 422) {
            // Validation errors from Laravel
            this.errors = err.response.data.errors;
          } else {
            this.error = "Invalid email or password!";
          }
        }
      },
    },
  });
  </script>
  
  <style>
  /* Center content properly */
  .min-vh-100 {
    min-height: 100vh;
  }
  </style>
  