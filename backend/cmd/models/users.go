package models

import (
	"log"
	"math/rand"

	"golang.org/x/crypto/bcrypt"
	"gorm.io/gorm"
)

type UserRole string

const (
	Admin   UserRole = "admin"
	Student UserRole = "student"
)

type User struct {
	gorm.Model
	Username      string   `gorm:"size:255;not null;unique" json:"username"`
	NameFirst     string   `gorm:"size:255;not null" json:"name_first"`
	Email         string   `gorm:"size:255;not null;unique" json:"email"`
	Password      string   `gorm:"size:255;not null" json:"-"`
	PasswordReset bool     `gorm:"default:true" json:"password_reset"`
	NameLast      string   `gorm:"size:255;not null" json:"name_last"`
	Role          UserRole `gorm:"size:255;default student" json:"role"`

	/* foreign key */
	ProviderLogins []ProviderUserMapping `gorm:"foreignKey:UserID" json:"-"`
}

func (User) TableName() string {
	return "users"
}

const letterBytes = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"

func (user *User) CreateTempPassword() string {
	b := make([]byte, 8)
	for i := range b {
		b[i] = letterBytes[rand.Intn(len(letterBytes))]
	}
	return string(b)
}

func (user *User) HashPassword() error {
	log.Printf("Hashing password for user %s", user.Username)
	log.Printf("Password: %s", user.Password)
	bytes, err := bcrypt.GenerateFromPassword([]byte(user.Password), bcrypt.DefaultCost)
	if err != nil {
		return err
	}
	user.Password = string(bytes)
	log.Printf("Password hashed for user %s", user.Password)
	return nil
}

/**
* This function is called on a user object when it's fresh out of the database, so
* the password is already hashed and checked against the input string
* @param password string
* @return bool
**/
func (user *User) CheckPasswordHash(password string) bool {
	log.Printf("Checking password for user %s", user.Username)
	return bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(password)) == nil
}
