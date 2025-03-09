<template>
    <v-container class="d-flex justify-center align-center min-vh-100">
      <v-card class="pa-5" width="400">
        <v-card-title class="text-center text-h5">Register</v-card-title>
        <v-card-text>
          <v-form @submit.prevent="submitForm">
            <!-- Name Field -->
            <v-text-field 
              v-model="name" 
              label="Name" 
              required
              outlined
              density="comfortable"
              class="mb-3"
              :error-messages="errors.name"
            ></v-text-field>
  
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
  
            <!-- OTP Field -->
            <v-text-field 
              v-model="otp" 
              label="Enter OTP" 
              type="text" 
              outlined
              density="comfortable"
              class="mb-3"
              v-if="otpSent"
              :error-messages="errors.otp"
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
  
            <!-- Send OTP Button -->
            <v-btn v-if="!otpSent" color="orange" block @click="sendOtp">
              Send OTP
            </v-btn>
  
            <!-- Register Button -->
            <v-btn v-if="otpSent" type="submit" color="blue" block>
              Register
            </v-btn>
  
            <!-- Global Error Message -->
            <p v-if="error" class="text-red mt-3 text-center">{{ error }}</p>
  
            <!-- Login Link -->
            <p class="text-center mt-4">
              Already have an account? 
              <router-link to="/login" class="text-blue">Login here</router-link>
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
        name: "",
        email: "",
        password: "",
        otp: "",
        otpSent: false, // Track if OTP is sent
        error: "",
        errors: {}, // Stores validation errors
      };
    },
    methods: {
      async sendOtp() {
        this.error = "";
        this.errors = {};
  
        try {
          await axios.post("/send-otp", { email: this.email });
          this.otpSent = true;
        } catch (err) {
          if (err.response && err.response.status === 422) {
            this.errors = err.response.data.errors;
          } else {
            this.error = "Failed to send OTP!";
          }
        }
      },
      async submitForm() {
        this.error = "";
        this.errors = {};
  
        try {
          await axios.post("/register", {
            name: this.name,
            email: this.email,
            password: this.password,
            otp: this.otp,
          });
  
          this.$router.push("/login");
        } catch (err) {
          if (err.response && err.response.status === 422) {
            this.errors = err.response.data.errors;
          } else {
            this.error = "Registration failed!";
          }
        }
      },
    },
  });
  </script>
  
  <style>
  .min-vh-100 {
    min-height: 100vh;
  }
  </style>
  