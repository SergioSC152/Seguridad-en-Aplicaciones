def calcular_fisica():
    print("Analisis de accidentes ")

    while True:
        try:
            masa = float(input("\nIngrese la masa (en kg): "))
            velocidad_kmh = float(input("Ingrese la velocidad (en km/h): "))
            
            if masa < 0 or velocidad_kmh < 0:
                print("Error: La masa y la velocidad no pueden ser negativas.")
            else:
                
                velocidad_ms = velocidad_kmh / 3.6

                
                momento_lineal = masa * velocidad_ms

                
                energia_cinetica = 0.5 * masa * (velocidad_ms ** 2)

                # Mostrar resultados
                print("\n--- RESULTADOS ---")
                print(f"Velocidad:          {velocidad_ms:.2f} m/s")
                print(f"Momento lineal:     {momento_lineal:.2f} kg*m/s")
                print(f"Energía cinética:   {energia_cinetica:.2f} J")

        except ValueError:
            print("Error: Por favor, ingrese un número válido.")

        continuar = input("\n¿Desea realizar otro cálculo? (s/n): ").strip().lower()
        if continuar != 's':
            print("¡Gracias por usar la calculadora!")
            break

if __name__ == "__main__":
    calcular_fisica()