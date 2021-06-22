package com.example.rabbit.demo;

import java.io.IOException;

import com.example.rabbit.demo.models.Person;

import org.springframework.amqp.AmqpRejectAndDontRequeueException;
import org.springframework.amqp.core.Message;
import org.springframework.amqp.rabbit.annotation.RabbitListener;
import org.springframework.amqp.rabbit.core.RabbitTemplate;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

@Component
public class MessageReceiver {
    
    @Autowired
    private RabbitTemplate rabbitTemplate;


    public Person PersonInformationChanged(Person payload) throws IOException
    {               
        System.out.println(" [x] Received '" + payload.Name + " " + payload.Surname + "'");

        return payload;
    }

    public void ExceptionExample(String message) throws AmqpRejectAndDontRequeueException
    {
        try {
            //Obrada poruke....
            System.out.println("Primljena poruka");
            throw new Exception("Error while proccessing message");
        } catch (Exception e) {            
            throw new AmqpRejectAndDontRequeueException(e.toString());
        }
    }

    @RabbitListener(queues = "deadLetterQueue")
    public void ProcessFailedMessagesWithRetry(Message failedMessage){
        Integer retriesCount = (Integer) failedMessage.getMessageProperties()
            .getHeaders().get("x-reties-count");
        if (retriesCount == null) retriesCount = 1;
        if (retriesCount > 5) {
            System.out.println("Discard message");
            rabbitTemplate.send("demo.parkingLot.exchange", 
                    "demo.parkingLot.binding", failedMessage);
            return;
        }

        System.out.println("Retrying message for the " + retriesCount + " time");
        failedMessage.getMessageProperties()
            .getHeaders().put("x-reties-count", ++retriesCount);
        rabbitTemplate.send("demo.errorMaking.exchange", 
            "demo.errorMaking.binding", failedMessage);
    }
}
