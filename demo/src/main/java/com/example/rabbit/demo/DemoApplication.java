package com.example.rabbit.demo;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.context.support.AbstractApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;

import java.io.IOException;
import java.util.concurrent.TimeoutException;

import com.rabbitmq.client.Channel;
import com.rabbitmq.client.Connection;
import com.rabbitmq.client.ConnectionFactory;
import com.rabbitmq.client.DeliverCallback;

@SpringBootApplication
public class DemoApplication {

    private final static String QUEUE_NAME = "testniQueue";
	public static void main(String[] args) throws IOException, TimeoutException {
		SpringApplication.run(DemoApplication.class, args);		

        @SuppressWarnings("resource")
        AbstractApplicationContext context = new ClassPathXmlApplicationContext("classpath:context.xml");

        context.registerShutdownHook();

		// ConnectionFactory factory = new ConnectionFactory();
        // factory.setHost("localhost");
        // Connection connection = factory.newConnection();
        // Channel channel = connection.createChannel();

        // channel.queueDeclare(QUEUE_NAME, false, false, false, null);
        // System.out.println(" [*] Waiting for messages. To exit press CTRL+C");

        // DeliverCallback deliverCallback = (consumerTag, delivery) -> {
        //     String message = new String(delivery.getBody(), "UTF-8");
        //     System.out.println(" [x] Received '" + message + "'");
        // };
        //channel.basicConsume(QUEUE_NAME, true, deliverCallback, consumerTag -> { });
	}

}
